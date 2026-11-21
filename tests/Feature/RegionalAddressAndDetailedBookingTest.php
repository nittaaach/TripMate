<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Village;
use App\Services\Booking\BookingService;
use Database\Seeders\IndonesiaRegionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegionalAddressAndDetailedBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(IndonesiaRegionSeeder::class);
    }

    public function test_can_fetch_provinces_via_regional_endpoint(): void
    {
        $response = $this->getJson(route('regional.provinces'));

        $response->assertOk()
            ->assertJsonFragment(['id' => '34', 'name' => 'DI Yogyakarta'])
            ->assertJsonFragment(['id' => '31', 'name' => 'DKI Jakarta'])
            ->assertJsonFragment(['id' => '51', 'name' => 'Bali']);
    }

    public function test_can_fetch_regencies_by_province(): void
    {
        $response = $this->getJson(route('regional.regencies', ['province_id' => '34']));

        $response->assertOk()
            ->assertJsonFragment(['id' => '3471', 'name' => 'Kota Yogyakarta'])
            ->assertJsonFragment(['id' => '3404', 'name' => 'Kabupaten Sleman']);
    }

    public function test_can_fetch_districts_by_regency(): void
    {
        $response = $this->getJson(route('regional.districts', ['regency_id' => '3471']));

        $response->assertOk()
            ->assertJsonFragment(['id' => '347101', 'name' => 'Gedongtengen'])
            ->assertJsonFragment(['id' => '347102', 'name' => 'Danurejan']);
    }

    public function test_can_fetch_villages_by_district(): void
    {
        $response = $this->getJson(route('regional.villages', ['district_id' => '347101']));

        $response->assertOk()
            ->assertJsonFragment([
                'id' => '34710101',
                'name' => 'Sosromenduran',
                'postal_code' => '55271',
            ]);
    }

    public function test_can_create_booking_with_maps_coordinates_and_indonesian_administrative_address(): void
    {
        $user = User::create([
            'name' => 'Siti Pelanggan',
            'email' => 'siti@example.com',
            'password' => 'secret123',
            'role' => UserRole::Customer,
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'TEST-BRIO',
            'brand' => 'Honda',
            'model' => 'Brio',
            'category' => 'city_car',
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'seats' => 5,
            'suitable_terrain' => ['city'],
            'suitable_road_conditions' => ['good'],
            'daily_rate' => 350000,
            'deposit_amount' => 400000,
            'is_available' => true,
            'status' => 'active',
        ]);

        $pickup = now()->addDays(1)->setHour(9)->setMinute(0)->format('Y-m-d H:i:s');
        $return = now()->addDays(3)->setHour(18)->setMinute(0)->format('Y-m-d H:i:s'); // 2 days

        /** @var BookingService $service */
        $service = app(BookingService::class);
        $booking = $service->createBooking($user, [
            'vehicle_id' => $vehicle->id,
            'pickup_datetime' => $pickup,
            'return_datetime' => $return,
            'pickup_address' => 'Jl. Sosrowijayan No. 12',
            'pickup_province_id' => '34',
            'pickup_regency_id' => '3471',
            'pickup_district_id' => '347101',
            'pickup_village_id' => '34710101',
            'pickup_rt_rw' => 'RT 02 / RW 01',
            'pickup_postal_code' => '55271',
            'pickup_latitude' => -7.7925000,
            'pickup_longitude' => 110.3658000,
            'return_address' => 'Jl. Sosrowijayan No. 12',
            'return_province_id' => '34',
            'return_regency_id' => '3471',
            'return_district_id' => '347101',
            'return_village_id' => '34710101',
            'return_rt_rw' => 'RT 02 / RW 01',
            'return_postal_code' => '55271',
            'return_latitude' => -7.7925000,
            'return_longitude' => 110.3658000,
            'needs_driver' => false,
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'pickup_province_id' => '34',
            'pickup_regency_id' => '3471',
            'pickup_district_id' => '347101',
            'pickup_village_id' => '34710101',
            'pickup_rt_rw' => 'RT 02 / RW 01',
            'pickup_postal_code' => '55271',
        ]);

        $this->assertTrue($booking->has_pickup_coordinates);
        $this->assertTrue($booking->has_return_coordinates);
        $this->assertStringContainsString('https://www.google.com/maps?q=-7.7925', $booking->pickup_maps_url);

        // Test formatted address accessor
        $formatted = $booking->formatted_pickup_address;
        $this->assertStringContainsString('Jl. Sosrowijayan No. 12', $formatted);
        $this->assertStringContainsString('Kel. Sosromenduran', $formatted);
        $this->assertStringContainsString('Kec. Gedongtengen', $formatted);
        $this->assertStringContainsString('Kota Yogyakarta', $formatted);
        $this->assertStringContainsString('DI Yogyakarta', $formatted);
        $this->assertStringContainsString('55271', $formatted);
    }

    public function test_customer_can_view_booking_create_page_with_map_and_provinces(): void
    {
        $user = User::create([
            'name' => 'Customer View',
            'email' => 'customer.view@example.com',
            'password' => 'secret123',
            'role' => UserRole::Customer,
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'TEST-CALYA',
            'brand' => 'Toyota',
            'model' => 'Calya',
            'category' => 'mpv',
            'transmission' => 'manual',
            'fuel_type' => 'petrol',
            'seats' => 7,
            'suitable_terrain' => ['city'],
            'suitable_road_conditions' => ['good'],
            'daily_rate' => 300000,
            'deposit_amount' => 400000,
            'is_available' => true,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('customer.bookings.create', ['vehicle_id' => $vehicle->id]));

        $response->assertOk()
            ->assertSee('Formulir Pemesanan')
            ->assertSee('pickup-map')
            ->assertSee('return-map')
            ->assertSee('DI Yogyakarta')
            ->assertSee('DKI Jakarta')
            ->assertSee('Total Pembayaran:');
    }

    public function test_vacation_plan_create_and_show_render_maps(): void
    {
        $user = User::create([
            'name' => 'Plan User',
            'email' => 'plan.user@example.com',
            'password' => 'secret123',
            'role' => UserRole::Customer,
        ]);

        $destination = \App\Models\Destination::create([
            'name' => 'Candi Borobudur',
            'slug' => 'candi-borobudur',
            'city' => 'Magelang',
            'province' => 'Jawa Tengah',
            'latitude' => -7.607873,
            'longitude' => 110.203751,
            'terrain_type' => 'mixed',
            'road_condition' => 'good',
            'is_active' => true,
        ]);

        // 1. Create page renders map
        $createRes = $this->actingAs($user)->get(route('customer.vacation-plans.create'));
        $createRes->assertOk()->assertSee('origin-map');

        // 2. Show page renders route map
        $plan = $user->vacationPlans()->create([
            'destination_id' => $destination->id,
            'origin_address' => 'Jl. Malioboro, Yogyakarta',
            'origin_latitude' => -7.7956,
            'origin_longitude' => 110.3695,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'passenger_count' => 4,
            'luggage_level' => 'medium',
            'trip_type' => 'round_trip',
        ]);

        $showRes = $this->actingAs($user)->get(route('customer.vacation-plans.show', $plan));
        $showRes->assertOk()->assertSee('plan-route-map');
    }

    public function test_destination_show_renders_map(): void
    {
        $destination = \App\Models\Destination::create([
            'name' => 'Pantai Parangtritis',
            'slug' => 'pantai-parangtritis',
            'city' => 'Bantul',
            'province' => 'DI Yogyakarta',
            'latitude' => -8.0255,
            'longitude' => 110.3340,
            'terrain_type' => 'beach',
            'road_condition' => 'good',
            'is_active' => true,
        ]);

        $response = $this->get(route('customer.destinations.show', $destination->slug));
        $response->assertOk()
            ->assertSee('destination-map')
            ->assertSee('Pantai Parangtritis');
    }

    public function test_admin_dashboard_renders_operational_monitoring_map(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@tripmate.com',
            'password' => 'secret123',
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk()
            ->assertSee('admin-monitoring-map')
            ->assertSee('Peta Monitoring Titik Serah Terima Booking Aktif');
    }

    public function test_admin_destination_create_and_edit_render_picker_map(): void
    {
        $admin = User::create([
            'name' => 'Admin Destination Map',
            'email' => 'admindestmap@tripmate.com',
            'password' => 'secret123',
            'role' => UserRole::Admin,
        ]);

        $destination = \App\Models\Destination::create([
            'name' => 'Tebing Breksi',
            'slug' => 'tebing-breksi',
            'city' => 'Sleman',
            'province' => 'DI Yogyakarta',
            'latitude' => -7.7820,
            'longitude' => 110.5045,
            'terrain_type' => 'mountain',
            'road_condition' => 'moderate',
            'is_active' => true,
        ]);

        $createRes = $this->actingAs($admin)->get(route('admin.destinations.create'));
        $createRes->assertOk()->assertSee('dest-picker-map');

        $editRes = $this->actingAs($admin)->get(route('admin.destinations.edit', $destination));
        $editRes->assertOk()->assertSee('dest-picker-map');
    }
}
