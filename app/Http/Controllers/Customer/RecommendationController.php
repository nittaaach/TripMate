<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\VacationPlan;
use App\Models\VehicleRecommendation;
use App\Services\Recommendation\RecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService,
    ) {}

    public function index(VacationPlan $vacationPlan): View
    {
        Gate::authorize('view', $vacationPlan);

        $vacationPlan->load(['destination', 'recommendations.vehicle.images', 'recommendations.vehicle.primaryImage']);
        $recommendations = $vacationPlan->recommendations;

        return view('customer.recommendations.index', compact('vacationPlan', 'recommendations'));
    }

    public function regenerate(VacationPlan $vacationPlan): RedirectResponse
    {
        Gate::authorize('update', $vacationPlan);

        $this->recommendationService->generateRecommendations($vacationPlan);

        return redirect()->route('customer.vacation-plans.recommendations', $vacationPlan)
            ->with('success', 'Rekomendasi armada berhasil diperbarui.');
    }

    public function select(VacationPlan $vacationPlan, VehicleRecommendation $recommendation): RedirectResponse
    {
        Gate::authorize('view', $vacationPlan);

        // Mark recommendation as selected
        $vacationPlan->recommendations()->update(['is_selected' => false]);
        $recommendation->update(['is_selected' => true]);

        return redirect()->route('customer.bookings.create', [
            'vehicle_id' => $recommendation->vehicle_id,
            'plan_id' => $vacationPlan->id,
        ]);
    }
}
