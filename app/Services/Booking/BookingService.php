<?php

namespace App\Services\Booking;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\VacationPlanStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\VacationPlan;
use App\Models\Vehicle;
use App\Models\VehicleRecommendation;
use App\Services\Availability\VehicleAvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        protected BookingPricingService $pricingService,
        protected VehicleAvailabilityService $availabilityService,
    ) {}

    /**
     * Create a new booking with pessimistic locking to prevent double booking.
     *
     * @param  array{
     *     vehicle_id: int,
     *     vacation_plan_id?: int|null,
     *     pickup_datetime: string,
     *     return_datetime: string,
     *     pickup_address: string,
     *     return_address: string,
     *     needs_driver?: bool,
     *     notes?: string|null
     * }  $data
     * @throws ValidationException
     */
    public function createBooking(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            $vehicleId = (int) $data['vehicle_id'];

            // 1. Lock the vehicle row to serialize concurrent booking attempts
            $vehicle = Vehicle::where('id', $vehicleId)->lockForUpdate()->firstOrFail();

            $pickup = Carbon::parse($data['pickup_datetime']);
            $return = Carbon::parse($data['return_datetime']);

            if ($return->lessThanOrEqualTo($pickup)) {
                throw ValidationException::withMessages([
                    'return_datetime' => 'Waktu pengembalian harus lebih lambat daripada waktu penjemputan.',
                ]);
            }

            // 2. Strict availability check within transaction
            if (!$this->availabilityService->isAvailable($vehicle, $pickup, $return)) {
                throw ValidationException::withMessages([
                    'vehicle_id' => 'Kendaraan ini tidak tersedia pada rentang tanggal/jam yang dipilih. Silakan pilih tanggal atau kendaraan lain.',
                ]);
            }

            // 3. Compute price snapshots
            $needsDriver = (bool) ($data['needs_driver'] ?? false);
            $pricing = $this->pricingService->calculate($vehicle, $pickup, $return, $needsDriver);

            // 4. Generate unique booking code: TM-YYYYMMDD-XXXXX
            $bookingCode = $this->generateUniqueBookingCode();

            // 5. Create booking record
            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'user_id' => $user->id,
                'vacation_plan_id' => $data['vacation_plan_id'] ?? null,
                'vehicle_id' => $vehicle->id,
                'pickup_datetime' => $pickup,
                'return_datetime' => $return,
                'pickup_address' => $data['pickup_address'],
                'return_address' => $data['return_address'],
                'rental_days' => $pricing['rental_days'],
                'daily_rate_snapshot' => $pricing['daily_rate_snapshot'],
                'subtotal' => $pricing['subtotal'],
                'discount_amount' => $pricing['discount_amount'],
                'additional_fee' => $pricing['additional_fee'],
                'deposit_amount' => $pricing['deposit_amount'],
                'total_amount' => $pricing['total_amount'],
                'status' => BookingStatus::Pending,
                'notes' => $data['notes'] ?? null,
            ]);

            // 6. Update vacation plan if linked
            if (!empty($data['vacation_plan_id'])) {
                $plan = VacationPlan::find($data['vacation_plan_id']);
                if ($plan) {
                    $plan->update(['status' => VacationPlanStatus::Booked]);

                    VehicleRecommendation::where('vacation_plan_id', $plan->id)
                        ->where('vehicle_id', $vehicle->id)
                        ->update(['is_selected' => true]);
                }
            }

            // 7. Create initial pending payment record for manual bank transfer
            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => PaymentMethod::BankTransfer,
                'amount' => $pricing['total_amount'],
                'currency' => 'IDR',
                'status' => PaymentStatus::Pending,
            ]);

            return $booking;
        });
    }

    /**
     * Generate unique booking code.
     */
    protected function generateUniqueBookingCode(): string
    {
        do {
            $code = 'TM-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }
}
