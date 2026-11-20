<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\Booking\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_booking_with_price_snapshots(): void
    {
        $user = User::create([
            'name' => 'Budi Customer',
            'email' => 'budi.customer@test.com',
            'password' => 'secret',
            'role' => UserRole::Customer,
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'TEST-AVZ',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'category' => 'mpv',
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'seats' => 7,
            'suitable_terrain' => ['city'],
            'suitable_road_conditions' => ['good'],
            'daily_rate' => 450000,
            'deposit_amount' => 500000,
            'is_available' => true,
            'status' => 'active',
        ]);

        $pickup = now()->addDays(2)->setHour(9)->setMinute(0)->format('Y-m-d H:i:s');
        $return = now()->addDays(4)->setHour(18)->setMinute(0)->format('Y-m-d H:i:s'); // 2 days

        /** @var BookingService $service */
        $service = app(BookingService::class);
        $booking = $service->createBooking($user, [
            'vehicle_id' => $vehicle->id,
            'pickup_datetime' => $pickup,
            'return_datetime' => $return,
            'pickup_address' => 'Jl. Kaliurang km 5',
            'return_address' => 'Jl. Kaliurang km 5',
            'needs_driver' => true,
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'status' => BookingStatus::Pending->value,
            'rental_days' => 2,
            'daily_rate_snapshot' => 450000.00,
            'subtotal' => 900000.00,
            'additional_fee' => 300000.00, // 2 days * 150.000 driver
            'total_amount' => 1200000.00,
        ]);

        // Automatically creates pending payment
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => 1200000.00,
            'status' => PaymentStatus::Pending->value,
        ]);
    }

    public function test_concurrent_or_conflicting_booking_is_prevented(): void
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@test.com',
            'password' => 'secret',
            'role' => UserRole::Customer,
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@test.com',
            'password' => 'secret',
            'role' => UserRole::Customer,
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'TEST-INNOVA',
            'brand' => 'Toyota',
            'model' => 'Innova',
            'category' => 'mpv',
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'seats' => 7,
            'suitable_terrain' => ['city'],
            'suitable_road_conditions' => ['good'],
            'daily_rate' => 600000,
            'deposit_amount' => 500000,
            'is_available' => true,
            'status' => 'active',
        ]);

        $pickup = now()->addDays(5)->setHour(8)->setMinute(0)->format('Y-m-d H:i:s');
        $return = now()->addDays(7)->setHour(18)->setMinute(0)->format('Y-m-d H:i:s');

        /** @var BookingService $service */
        $service = app(BookingService::class);

        // First user books successfully
        $service->createBooking($user1, [
            'vehicle_id' => $vehicle->id,
            'pickup_datetime' => $pickup,
            'return_datetime' => $return,
            'pickup_address' => 'Hotel A',
            'return_address' => 'Hotel A',
        ]);

        // Second user attempts to book the same vehicle on overlapping dates
        $this->expectException(ValidationException::class);
        $service->createBooking($user2, [
            'vehicle_id' => $vehicle->id,
            'pickup_datetime' => now()->addDays(6)->setHour(8)->setMinute(0)->format('Y-m-d H:i:s'),
            'return_datetime' => now()->addDays(8)->setHour(18)->setMinute(0)->format('Y-m-d H:i:s'),
            'pickup_address' => 'Hotel B',
            'return_address' => 'Hotel B',
        ]);
    }

    public function test_customer_can_upload_payment_proof_and_staff_can_verify(): void
    {
        Storage::fake('public');

        $customer = User::create([
            'name' => 'Customer Test',
            'email' => 'customer.test@test.com',
            'password' => 'secret',
            'role' => UserRole::Customer,
        ]);

        $staff = User::create([
            'name' => 'Staff Test',
            'email' => 'staff.test@test.com',
            'password' => 'secret',
            'role' => UserRole::Staff,
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'VEH-01',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'category' => 'mpv',
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'seats' => 7,
            'suitable_terrain' => ['city'],
            'suitable_road_conditions' => ['good'],
            'daily_rate' => 400000,
            'deposit_amount' => 500000,
            'is_available' => true,
            'status' => 'active',
        ]);

        $booking = app(BookingService::class)->createBooking($customer, [
            'vehicle_id' => $vehicle->id,
            'pickup_datetime' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'return_datetime' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'pickup_address' => 'Lokasi Penjemputan',
            'return_address' => 'Lokasi Pengembalian',
        ]);

        // Customer uploads payment proof
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');
        $response = $this->actingAs($customer)->post(route('customer.bookings.payment.store', $booking), [
            'proof_file' => $file,
            'notes' => 'Transfer via BCA',
        ]);

        $response->assertRedirect(route('customer.bookings.show', $booking));

        $payment = $booking->fresh()->latestPayment;
        $this->assertNotNull($payment->proof_path);
        Storage::disk('public')->assertExists($payment->proof_path);

        // Staff verifies the payment
        $verifyResponse = $this->actingAs($staff)->patch(route('admin.payments.verify', $payment));
        $verifyResponse->assertRedirect();

        $this->assertEquals(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertEquals(BookingStatus::Confirmed, $booking->fresh()->status);
    }
}
