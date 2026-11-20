<?php

namespace App\Services\Recommendation;

use App\Models\VacationPlan;
use App\Models\Vehicle;

class VehicleScorer
{
    /**
     * Compute scoring breakdown and overall match score (0 - 100).
     *
     * @return array{
     *     match_score: float,
     *     terrain_fit_score: float,
     *     capacity_fit_score: float,
     *     distance_fit_score: float,
     *     budget_fit_score: float,
     *     comfort_fit_score: float
     * }
     */
    public function score(Vehicle $vehicle, VacationPlan $plan): array
    {
        $terrainScore = $this->calculateTerrainScore($vehicle, $plan);
        $capacityScore = $this->calculateCapacityScore($vehicle, $plan);
        $distanceScore = $this->calculateDistanceScore($vehicle, $plan);
        $budgetScore = $this->calculateBudgetScore($vehicle, $plan);
        $comfortScore = $this->calculateComfortScore($vehicle, $plan);

        // Weighted sum: 30% terrain, 20% capacity, 20% distance, 20% budget, 10% comfort
        $matchScore = ($terrainScore * 0.30) +
                      ($capacityScore * 0.20) +
                      ($distanceScore * 0.20) +
                      ($budgetScore * 0.20) +
                      ($comfortScore * 0.10);

        return [
            'match_score' => round(min(100, max(0, $matchScore)), 2),
            'terrain_fit_score' => round($terrainScore, 2),
            'capacity_fit_score' => round($capacityScore, 2),
            'distance_fit_score' => round($distanceScore, 2),
            'budget_fit_score' => round($budgetScore, 2),
            'comfort_fit_score' => round($comfortScore, 2),
        ];
    }

    protected function calculateTerrainScore(Vehicle $vehicle, VacationPlan $plan): float
    {
        $destTerrain = $plan->destination?->terrain_type?->value ?? (string) $plan->destination?->terrain_type;
        $destRoad = $plan->destination?->road_condition?->value ?? (string) $plan->destination?->road_condition;

        $suitableTerrains = (array) ($vehicle->suitable_terrain ?? []);
        $suitableRoads = (array) ($vehicle->suitable_road_conditions ?? []);

        $score = 70.0; // Base score

        if (in_array($destTerrain, $suitableTerrains, true)) {
            $score += 15.0;
        }

        if (in_array($destRoad, $suitableRoads, true)) {
            $score += 15.0;
        }

        // Ground clearance bonus for mountain/rough/offroad
        if (in_array($destTerrain, ['mountain', 'offroad'], true) || in_array($destRoad, ['rough', 'offroad'], true)) {
            if ($vehicle->ground_clearance_mm && $vehicle->ground_clearance_mm >= 200) {
                $score += 10.0;
            } elseif ($vehicle->ground_clearance_mm && $vehicle->ground_clearance_mm < 160) {
                $score -= 15.0;
            }
        }

        return min(100.0, max(20.0, $score));
    }

    protected function calculateCapacityScore(Vehicle $vehicle, VacationPlan $plan): float
    {
        $passengers = $plan->passenger_count;
        $effectiveSeats = $plan->needs_driver ? max(1, $vehicle->seats - 1) : $vehicle->seats;

        // Seat ratio
        $seatRatio = $passengers / $effectiveSeats; // e.g. 4/5 = 0.8
        if ($seatRatio >= 0.6 && $seatRatio <= 0.85) {
            $seatScore = 95.0; // Ideal fit
        } elseif ($seatRatio > 0.85 && $seatRatio <= 1.0) {
            $seatScore = 85.0; // Full but fits
        } elseif ($seatRatio >= 0.3 && $seatRatio < 0.6) {
            $seatScore = 80.0; // A bit spacious
        } else {
            $seatScore = 65.0; // Oversized
        }

        // Luggage fit
        $luggageLevel = $plan->luggage_level?->value ?? (string) $plan->luggage_level;
        $luggageCap = $vehicle->luggage_capacity_l ?? 300;

        $luggageScore = 80.0;
        if ($luggageLevel === 'light') {
            $luggageScore = 90.0;
        } elseif ($luggageLevel === 'medium') {
            $luggageScore = $luggageCap >= 350 ? 95.0 : 70.0;
        } elseif ($luggageLevel === 'heavy') {
            $luggageScore = $luggageCap >= 500 ? 100.0 : ($luggageCap >= 400 ? 75.0 : 50.0);
        }

        return ($seatScore * 0.60) + ($luggageScore * 0.40);
    }

    protected function calculateDistanceScore(Vehicle $vehicle, VacationPlan $plan): float
    {
        $distance = (float) ($plan->estimated_distance_km ?? 100.0);

        // For short trips (< 50 km), any car is great
        if ($distance < 50) {
            return 90.0;
        }

        $score = 80.0;

        // If long distance (> 150 km)
        if ($distance > 150) {
            if ($vehicle->fuel_type?->value === 'diesel' || $vehicle->fuel_type?->value === 'hybrid') {
                $score += 15.0; // Diesel & Hybrid shine on long distance
            } elseif ($vehicle->fuel_efficiency_km_per_l && $vehicle->fuel_efficiency_km_per_l >= 14.0) {
                $score += 10.0;
            }

            // If EV, check battery range
            if ($vehicle->fuel_type?->value === 'electric') {
                if ($vehicle->battery_range_km && $vehicle->battery_range_km < ($distance * 1.3)) {
                    $score -= 25.0; // Requires charging mid-trip
                } else {
                    $score += 10.0;
                }
            }
        }

        return min(100.0, max(30.0, $score));
    }

    protected function calculateBudgetScore(Vehicle $vehicle, VacationPlan $plan): float
    {
        if (!$plan->budget_per_day || $plan->budget_per_day <= 0) {
            return 80.0; // Default neutral score when customer has no budget preference
        }

        $budget = (float) $plan->budget_per_day;
        $rate = (float) $vehicle->daily_rate;

        if ($rate <= $budget) {
            // Under budget: very high score
            $savingsRatio = ($budget - $rate) / $budget;
            return min(100.0, 90.0 + ($savingsRatio * 10.0));
        }

        // Over budget: penalize proportionally
        $overRatio = ($rate - $budget) / $budget; // e.g. 10% over -> 0.10
        if ($overRatio <= 0.15) {
            return 75.0;
        } elseif ($overRatio <= 0.30) {
            return 60.0;
        } elseif ($overRatio <= 0.50) {
            return 40.0;
        }

        return 20.0;
    }

    protected function calculateComfortScore(Vehicle $vehicle, VacationPlan $plan): float
    {
        $score = 75.0;

        if ($vehicle->has_ac) {
            $score += 15.0;
        }

        if ($plan->needs_driver && $vehicle->has_driver_option ?? true) {
            $score += 10.0;
        }

        return min(100.0, max(40.0, $score));
    }
}
