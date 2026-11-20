<?php

namespace Tests\Feature;

use App\Enums\RoadCondition;
use App\Enums\TerrainType;
use App\Enums\UserRole;
use App\Models\Destination;
use App\Models\User;
use App\Models\VacationPlan;
use App\Models\Vehicle;
use App\Services\Recommendation\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_recommendation_engine_ranks_suitable_vehicles_and_disqualifies_ineligible(): void
    {
        $user = User::create([
            'name' => 'Wisatawan',
            'email' => 'wisata@test.com',
            'password' => 'secret',
            'role' => UserRole::Customer,
        ]);

        // Destination: Offroad mountain
        $dest = Destination::create([
            'name' => 'Gunung Bromo Lautan Pasir',
            'slug' => 'gunung-bromo',
            'city' => 'Probolinggo',
            'terrain_type' => TerrainType::Offroad,
            'road_condition' => RoadCondition::Offroad,
            'is_active' => true,
        ]);

        // 1. Vehicle 4x4 (Pajero) - Should pass & score highest
        $pajero = Vehicle::create([
            'vehicle_code' => 'PAJ-01',
            'brand' => 'Mitsubishi',
            'model' => 'Pajero Sport',
            'category' => 'suv',
            'transmission' => 'automatic',
            'fuel_type' => 'diesel',
            'seats' => 7,
            'ground_clearance_mm' => 218,
            'suitable_terrain' => ['mountain', 'offroad', 'city'],
            'suitable_road_conditions' => ['rough', 'offroad'],
            'daily_rate' => 1200000,
            'deposit_amount' => 1000000,
            'is_available' => true,
            'status' => 'active',
        ]);

        // 2. City Car (Brio) - Does not support offroad terrain -> MUST be disqualified by hard filter
        $brio = Vehicle::create([
            'vehicle_code' => 'BRI-01',
            'brand' => 'Honda',
            'model' => 'Brio',
            'category' => 'city_car',
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'seats' => 5,
            'ground_clearance_mm' => 165,
            'suitable_terrain' => ['city'],
            'suitable_road_conditions' => ['good'],
            'daily_rate' => 350000,
            'deposit_amount' => 500000,
            'is_available' => true,
            'status' => 'active',
        ]);

        // Vacation plan for 4 people to Bromo
        $plan = VacationPlan::create([
            'user_id' => $user->id,
            'destination_id' => $dest->id,
            'origin_address' => 'Surabaya',
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(4)->format('Y-m-d'),
            'passenger_count' => 4,
            'luggage_level' => 'medium',
            'trip_type' => 'round_trip',
            'needs_driver' => false,
            'status' => 'draft',
        ]);

        /** @var RecommendationService $service */
        $service = app(RecommendationService::class);
        $recommendations = $service->generateRecommendations($plan);

        // Brio should be filtered out, Pajero must be present
        $this->assertTrue($recommendations->contains('vehicle_id', $pajero->id));
        $this->assertFalse($recommendations->contains('vehicle_id', $brio->id));

        $topRec = $recommendations->first();
        $this->assertEquals($pajero->id, $topRec->vehicle_id);
        $this->assertGreaterThan(70, (float) $topRec->match_score);
        $this->assertNotEmpty($topRec->recommendation_reason);
    }

    public function test_driver_needs_adjusts_effective_seats_capacity(): void
    {
        $user = User::create([
            'name' => 'Wisatawan',
            'email' => 'driver-test@test.com',
            'password' => 'secret',
            'role' => UserRole::Customer,
        ]);

        $dest = Destination::create([
            'name' => 'Borobudur',
            'slug' => 'borobudur-city',
            'city' => 'Magelang',
            'terrain_type' => TerrainType::Highway,
            'road_condition' => RoadCondition::Good,
            'is_active' => true,
        ]);

        // Vehicle with 5 seats
        $car5Seats = Vehicle::create([
            'vehicle_code' => 'CAR-5S',
            'brand' => 'Honda',
            'model' => 'City',
            'category' => 'sedan',
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'seats' => 5, // With driver, only 4 passenger seats available
            'suitable_terrain' => ['city', 'highway'],
            'suitable_road_conditions' => ['good'],
            'daily_rate' => 450000,
            'deposit_amount' => 500000,
            'is_available' => true,
            'status' => 'active',
        ]);

        // Plan has 5 passengers AND needs_driver = true
        // Effective seats needed: 5 passengers + 1 driver = 6 seats. Car with 5 seats MUST be disqualified!
        $plan = VacationPlan::create([
            'user_id' => $user->id,
            'destination_id' => $dest->id,
            'origin_address' => 'Yogyakarta',
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'passenger_count' => 5,
            'luggage_level' => 'light',
            'trip_type' => 'round_trip',
            'needs_driver' => true,
            'status' => 'draft',
        ]);

        /** @var RecommendationService $service */
        $service = app(RecommendationService::class);
        $recommendations = $service->generateRecommendations($plan);

        // 5-seater car must NOT be recommended for 5 passengers + driver
        $this->assertFalse($recommendations->contains('vehicle_id', $car5Seats->id));
    }
}
