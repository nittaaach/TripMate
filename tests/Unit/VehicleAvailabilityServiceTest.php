<?php

namespace Tests\Unit;

use App\Enums\BookingStatus;
use App\Enums\MaintenanceStatus;
use App\Enums\VehicleStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Services\Availability\VehicleAvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleAvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_vehicle_is_available_when_no_conflicts_exist(): void
    {
        $vehicle = Vehicle::create([
            'vehicle_code' => 'TEST-01',
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
            'status' => VehicleStatus::Active,
        ]);

        $service = new VehicleAvailabilityService();

        $available = $service->isAvailable($vehicle, '2026-12-01 09:00:00', '2026-12-03 18:00:00');
        $this->assertTrue($available);
    }

    public function test_vehicle_is_not_available_when_booking_overlaps(): void
    {
        $user = User::create([
            'name' => 'Tester',
            'email' => 'tester@test.com',
            'password' => 'secret',
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'TEST-02',
            'brand' => 'Honda',
            'model' => 'Brio',
            'category' => 'city_car',
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'seats' => 5,
            'suitable_terrain' => ['city'],
            'suitable_road_conditions' => ['good'],
            'daily_rate' => 350000,
            'deposit_amount' => 500000,
            'is_available' => true,
            'status' => VehicleStatus::Active,
        ]);

        // Existing confirmed booking from 10 Dec to 15 Dec
        Booking::create([
            'booking_code' => 'CONF-001',
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'pickup_datetime' => '2026-12-10 09:00:00',
            'return_datetime' => '2026-12-15 18:00:00',
            'pickup_address' => 'Jakarta',
            'return_address' => 'Jakarta',
            'rental_days' => 5,
            'daily_rate_snapshot' => 350000,
            'subtotal' => 1750000,
            'deposit_amount' => 500000,
            'total_amount' => 1750000,
            'status' => BookingStatus::Confirmed,
        ]);

        $service = new VehicleAvailabilityService();

        // 1. Overlapping range: 12 Dec to 14 Dec (inside existing booking) -> FALSE
        $this->assertFalse($service->isAvailable($vehicle, '2026-12-12 09:00:00', '2026-12-14 18:00:00'));

        // 2. Overlapping range: 8 Dec to 12 Dec (straddling start) -> FALSE
        $this->assertFalse($service->isAvailable($vehicle, '2026-12-08 09:00:00', '2026-12-12 18:00:00'));

        // 3. Non-overlapping: 16 Dec to 18 Dec (after existing booking) -> TRUE
        $this->assertTrue($service->isAvailable($vehicle, '2026-12-16 09:00:00', '2026-12-18 18:00:00'));
    }

    public function test_vehicle_is_not_available_when_under_maintenance(): void
    {
        $vehicle = Vehicle::create([
            'vehicle_code' => 'TEST-03',
            'brand' => 'Mitsubishi',
            'model' => 'Pajero',
            'category' => 'suv',
            'transmission' => 'automatic',
            'fuel_type' => 'diesel',
            'seats' => 7,
            'suitable_terrain' => ['offroad'],
            'suitable_road_conditions' => ['rough'],
            'daily_rate' => 1000000,
            'deposit_amount' => 1000000,
            'is_available' => true,
            'status' => VehicleStatus::Active,
        ]);

        VehicleMaintenance::create([
            'vehicle_id' => $vehicle->id,
            'start_datetime' => '2026-12-05 08:00:00',
            'end_datetime' => '2026-12-07 18:00:00',
            'description' => 'Servis berkala',
            'status' => MaintenanceStatus::Scheduled,
        ]);

        $service = new VehicleAvailabilityService();

        // Overlaps maintenance
        $this->assertFalse($service->isAvailable($vehicle, '2026-12-06 09:00:00', '2026-12-08 18:00:00'));
        // Free period
        $this->assertTrue($service->isAvailable($vehicle, '2026-12-08 09:00:00', '2026-12-10 18:00:00'));
    }
}
