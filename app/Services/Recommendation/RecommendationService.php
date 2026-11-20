<?php

namespace App\Services\Recommendation;

use App\Enums\VacationPlanStatus;
use App\Models\VacationPlan;
use App\Models\Vehicle;
use App\Models\VehicleRecommendation;
use App\Services\Routing\RoutingServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function __construct(
        protected RoutingServiceInterface $routingService,
        protected VehicleEligibilityFilter $eligibilityFilter,
        protected VehicleScorer $scorer,
        protected RecommendationReasonBuilder $reasonBuilder,
    ) {}

    /**
     * Generate and persist ranked vehicle recommendations for a vacation plan.
     *
     * @param  VacationPlan  $plan
     * @return Collection<int, VehicleRecommendation>
     */
    public function generateRecommendations(VacationPlan $plan): Collection
    {
        $plan->loadMissing('destination');

        // 1. Resolve and persist route snapshot if not yet calculated
        if ($plan->estimated_distance_km === null && $plan->destination) {
            $route = $this->routingService->estimate(
                originAddress: $plan->origin_address,
                originLat: $plan->origin_latitude ? (float) $plan->origin_latitude : null,
                originLng: $plan->origin_longitude ? (float) $plan->origin_longitude : null,
                destLat: $plan->destination->latitude ? (float) $plan->destination->latitude : null,
                destLng: $plan->destination->longitude ? (float) $plan->destination->longitude : null,
            );

            if ($route->success) {
                $plan->update([
                    'estimated_distance_km' => $route->distanceKm,
                    'estimated_duration_minutes' => $route->durationMinutes,
                    'distance_source' => $route->source,
                ]);
            }
        }

        // 2. Fetch candidate vehicles
        $candidates = Vehicle::query()
            ->with(['images', 'primaryImage'])
            ->get();

        // 3. Hard eligibility filter
        $eligibleVehicles = $this->eligibilityFilter->filter($candidates, $plan);

        // 4. Score and sort candidates
        $scored = $eligibleVehicles->map(function (Vehicle $vehicle) use ($plan) {
            $scores = $this->scorer->score($vehicle, $plan);
            $reason = $this->reasonBuilder->build($vehicle, $plan, $scores);

            return [
                'vehicle' => $vehicle,
                'scores' => $scores,
                'reason' => $reason,
            ];
        })->sortByDesc(fn ($item) => $item['scores']['match_score'])->values();

        // 5. Persist recommendations in database transaction
        return DB::transaction(function () use ($plan, $scored) {
            // Remove old recommendations for this plan
            $plan->recommendations()->delete();

            $results = collect();
            $rank = 1;

            foreach ($scored as $item) {
                $rec = VehicleRecommendation::create([
                    'vacation_plan_id' => $plan->id,
                    'vehicle_id' => $item['vehicle']->id,
                    'rank_position' => $rank++,
                    'match_score' => $item['scores']['match_score'],
                    'distance_fit_score' => $item['scores']['distance_fit_score'],
                    'capacity_fit_score' => $item['scores']['capacity_fit_score'],
                    'terrain_fit_score' => $item['scores']['terrain_fit_score'],
                    'budget_fit_score' => $item['scores']['budget_fit_score'],
                    'recommendation_reason' => $item['reason'],
                    'is_selected' => false,
                    'algorithm_version' => 'v1.0-rule-based',
                ]);

                $results->push($rec);
            }

            if ($results->isNotEmpty()) {
                $plan->update(['status' => VacationPlanStatus::Recommended]);
            }

            return $results;
        });
    }
}
