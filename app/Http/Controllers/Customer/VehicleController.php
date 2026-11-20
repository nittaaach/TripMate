<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\Availability\VehicleAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function __construct(
        protected VehicleAvailabilityService $availabilityService,
    ) {}

    public function index(Request $request): View
    {
        $query = Vehicle::active()->with(['images', 'primaryImage']);

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('transmission')) {
            $query->where('transmission', $request->input('transmission'));
        }

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->input('fuel_type'));
        }

        if ($request->filled('min_seats')) {
            $query->where('seats', '>=', (int) $request->input('min_seats'));
        }

        if ($request->filled('max_rate')) {
            $query->where('daily_rate', '<=', (float) $request->input('max_rate'));
        }

        $vehicles = $query->paginate(12)->withQueryString();

        // Optional date availability filter
        if ($request->filled('pickup_date') && $request->filled('return_date')) {
            $pickup = Carbon::parse($request->input('pickup_date'))->startOfDay();
            $return = Carbon::parse($request->input('return_date'))->endOfDay();

            $availableVehicles = $this->availabilityService->removeConflicts($vehicles->getCollection(), $pickup, $return);
            $vehicles->setCollection($availableVehicles);
        }

        return view('customer.vehicles.index', compact('vehicles'));
    }

    public function show(Vehicle $vehicle): View
    {
        $vehicle->load(['images', 'reviews.user']);

        return view('customer.vehicles.show', compact('vehicle'));
    }
}
