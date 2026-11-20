<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\VacationPlan;
use App\Models\Vehicle;
use App\Services\Booking\BookingPricingService;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected BookingPricingService $pricingService,
    ) {}

    public function index(Request $request): View
    {
        $bookings = $request->user()->bookings()
            ->with(['vehicle.primaryImage', 'latestPayment'])
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function create(Request $request): View
    {
        $vehicle = Vehicle::active()->findOrFail($request->input('vehicle_id'));
        $plan = null;

        $pickupDatetime = now()->addDay()->setHour(9)->setMinute(0);
        $returnDatetime = now()->addDays(2)->setHour(18)->setMinute(0);
        $pickupAddress = '';
        $returnAddress = '';
        $needsDriver = false;

        if ($request->filled('plan_id')) {
            $plan = VacationPlan::find($request->input('plan_id'));
            if ($plan && $plan->user_id === $request->user()->id) {
                $pickupDatetime = Carbon::parse($plan->start_date)->setHour(8)->setMinute(0);
                $returnDatetime = Carbon::parse($plan->end_date)->setHour(18)->setMinute(0);
                $pickupAddress = $plan->origin_address;
                $returnAddress = $plan->origin_address;
                $needsDriver = (bool) $plan->needs_driver;
            }
        }

        $pricing = $this->pricingService->calculate($vehicle, $pickupDatetime, $returnDatetime, $needsDriver);

        return view('customer.bookings.create', compact(
            'vehicle',
            'plan',
            'pickupDatetime',
            'returnDatetime',
            'pickupAddress',
            'returnAddress',
            'needsDriver',
            'pricing'
        ));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $booking = $this->bookingService->createBooking(
            $request->user(),
            $request->validated(),
        );

        return redirect()->route('customer.bookings.payment', $booking)
            ->with('success', 'Reservasi berhasil dibuat! Silakan selesaikan pembayaran.');
    }

    public function show(Booking $booking): View
    {
        Gate::authorize('view', $booking);

        $booking->load(['vehicle.primaryImage', 'payments', 'review']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        Gate::authorize('cancel', $booking);

        $booking->update([
            'status' => BookingStatus::Cancelled,
        ]);

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Booking berhasil dibatalkan.');
    }
}
