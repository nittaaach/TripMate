<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MaintenanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $maintenances = VehicleMaintenance::query()
            ->with('vehicle')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest('start_datetime')
            ->paginate(15)
            ->withQueryString();

        return view('admin.maintenance.index', compact('maintenances'));
    }

    public function create(Request $request): View
    {
        $vehicles = Vehicle::orderBy('brand')->orderBy('model')->get();
        $selectedVehicleId = $request->input('vehicle_id');

        return view('admin.maintenance.create', compact('vehicles', 'selectedVehicleId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['nullable', 'date', 'after_or_equal:start_datetime'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
        ]);

        VehicleMaintenance::create($validated);

        return redirect()->route('admin.maintenance.index')
            ->with('success', 'Jadwal perawatan kendaraan berhasil ditambahkan.');
    }

    public function edit(VehicleMaintenance $maintenance): View
    {
        $vehicles = Vehicle::orderBy('brand')->orderBy('model')->get();

        return view('admin.maintenance.edit', compact('maintenance', 'vehicles'));
    }

    public function update(Request $request, VehicleMaintenance $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['nullable', 'date', 'after_or_equal:start_datetime'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
        ]);

        $maintenance->update($validated);

        return redirect()->route('admin.maintenance.index')
            ->with('success', 'Data perawatan armada berhasil diperbarui.');
    }

    public function destroy(VehicleMaintenance $maintenance): RedirectResponse
    {
        $maintenance->delete();

        return redirect()->route('admin.maintenance.index')
            ->with('success', 'Jadwal perawatan berhasil dihapus.');
    }
}
