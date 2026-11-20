<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVacationPlanRequest;
use App\Models\Destination;
use App\Models\VacationPlan;
use App\Services\Recommendation\RecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VacationPlanController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService,
    ) {}

    public function index(Request $request): View
    {
        $plans = $request->user()->vacationPlans()
            ->with(['destination', 'recommendations.vehicle'])
            ->latest()
            ->paginate(10);

        return view('customer.vacation-plans.index', compact('plans'));
    }

    public function create(): View
    {
        $destinations = Destination::active()->orderBy('name')->get();

        return view('customer.vacation-plans.create', compact('destinations'));
    }

    public function store(StoreVacationPlanRequest $request): RedirectResponse
    {
        $plan = $request->user()->vacationPlans()->create($request->validated());

        // Automatically run recommendation engine
        $this->recommendationService->generateRecommendations($plan);

        return redirect()->route('customer.vacation-plans.recommendations', $plan)
            ->with('success', 'Rencana perjalanan berhasil disimpan! Berikut rekomendasi armada yang sesuai.');
    }

    public function show(VacationPlan $vacationPlan): View
    {
        Gate::authorize('view', $vacationPlan);

        $vacationPlan->load(['destination', 'recommendations.vehicle.primaryImage']);

        return view('customer.vacation-plans.show', compact('vacationPlan'));
    }

    public function destroy(VacationPlan $vacationPlan): RedirectResponse
    {
        Gate::authorize('delete', $vacationPlan);

        $vacationPlan->delete();

        return redirect()->route('customer.vacation-plans.index')
            ->with('success', 'Rencana liburan berhasil dihapus.');
    }
}
