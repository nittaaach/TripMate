<?php

namespace App\Services\Recommendation;

use App\Enums\VehicleStatus;
use App\Models\VacationPlan;
use App\Models\Vehicle;
use App\Services\Availability\VehicleAvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VehicleEligibilityFilter
{
    public function __construct(
        protected VehicleAvailabilityService $availabilityService,
    ) {}

    /**
     * Filter candidates using hard criteria.
     *
     * @param  Collection<int, Vehicle>  $vehicles
     * @return Collection<int, Vehicle>
     */
    public function filter(Collection $vehicles, VacationPlan $plan): Collection
    {
        $pickupDatetime = Carbon::parse($plan->start_date)->startOfDay();
        $returnDatetime = Carbon::parse($plan->end_date)->endOfDay();

        return $vehicles->filter(function (Vehicle $vehicle) use ($plan, $pickupDatetime, $returnDatetime) {
            // 1. Must be active and generally available
            if ($vehicle->status !== VehicleStatus::Active || !$vehicle->is_available) {
                return false;
            }

            // 2. Capacity: seats includes driver.
            // If customer rents with driver, customer passenger capacity is (seats - 1).
            $effectiveSeats = $plan->needs_driver ? max(0, $vehicle->seats - 1) : $vehicle->seats;
            if ($effectiveSeats < $plan->passenger_count) {
                return false;
            }

            // 3. Driver constraint: if vehicle strictly requires driver, but plan forbids or doesn't need it
            // (In TripMate, requires_driver means it cannot be self-driven)
            if ($vehicle->requires_driver && !$plan->needs_driver) {
                return false;
            }

            // 4. Hard budget limit check (if customer specified a strict budget)
            if ($plan->budget_per_day && $plan->budget_per_day > 0) {
                // If daily rate exceeds 150% of budget, discard as out-of-reach
                if ((float) $vehicle->daily_rate > ((float) $plan->budget_per_day * 1.5)) {
                    return false;
                }
            }

            // 5. Terrain & Road suitability
            $destTerrain = $plan->destination?->terrain_type?->value ?? (string) $plan->destination?->terrain_type;
            $destRoad = $plan->destination?->road_condition?->value ?? (string) $plan->destination?->road_condition;

            $suitableTerrains = (array) ($vehicle->suitable_terrain ?? []);
            $suitableRoads = (array) ($vehicle->suitable_road_conditions ?? []);

            // If destination requires offroad or rough road, vehicle must support it
            if ($destTerrain === 'offroad' && !in_array('offroad', $suitableTerrains, true)) {
                return false;
            }

            if ($destRoad === 'offroad' && !in_array('offroad', $suitableRoads, true)) {
                return false;
            }

            if ($destRoad === 'rough' && !in_array('rough', $suitableRoads, true) && !in_array('offroad', $suitableRoads, true)) {
                return false;
            }

            // 6. Availability: check conflicts in booking and maintenance
            if (!$this->availabilityService->isAvailable($vehicle, $pickupDatetime, $returnDatetime)) {
                return false;
            }

            return true;
        });
    }
}
