<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_protected_customer_routes(): void
    {
        $this->get(route('customer.vacation-plans.create'))->assertRedirect(route('login'));
        $this->get(route('customer.bookings.index'))->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@test.com',
            'password' => 'secret',
            'role' => UserRole::Customer,
        ]);

        $this->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_and_staff_can_access_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'secret',
            'role' => UserRole::Admin,
        ]);

        $staff = User::create([
            'name' => 'Staff',
            'email' => 'staff@test.com',
            'password' => 'secret',
            'role' => UserRole::Staff,
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($staff)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_customer_cannot_view_other_customers_booking(): void
    {
        $customer1 = User::create([
            'name' => 'Customer 1',
            'email' => 'c1@test.com',
            'password' => 'secret',
            'role' => UserRole::Customer,
        ]);

        $customer2 = User::create([
            'name' => 'Customer 2',
            'email' => 'c2@test.com',
            'password' => 'secret',
            'role' => UserRole::Customer,
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'V-TEST',
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

        $booking = Booking::create([
            'booking_code' => 'TM-CUST-1',
            'user_id' => $customer1->id,
            'vehicle_id' => $vehicle->id,
            'pickup_datetime' => now()->addDays(1),
            'return_datetime' => now()->addDays(2),
            'pickup_address' => 'Jl. Solo',
            'return_address' => 'Jl. Solo',
            'rental_days' => 1,
            'daily_rate_snapshot' => 400000,
            'subtotal' => 400000,
            'total_amount' => 400000,
            'status' => 'pending',
        ]);

        // Customer 2 tries to view Customer 1's booking -> 403 Forbidden
        $this->actingAs($customer2)
            ->get(route('customer.bookings.show', $booking))
            ->assertForbidden();

        // Customer 1 views own booking -> OK
        $this->actingAs($customer1)
            ->get(route('customer.bookings.show', $booking))
            ->assertOk();
    }
}
