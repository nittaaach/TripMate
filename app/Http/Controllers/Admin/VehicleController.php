<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $vehicles = Vehicle::query()
            ->with(['primaryImage'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('vehicle_code', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        return view('admin.vehicles.create');
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['requires_driver'] = $request->boolean('requires_driver', false);
        $validated['has_ac'] = $request->boolean('has_ac', true);
        $validated['is_available'] = $request->boolean('is_available', true);

        $vehicle = Vehicle::create($validated);

        return redirect()->route('admin.vehicles.edit', $vehicle)
            ->with('success', 'Kendaraan berhasil ditambahkan. Anda dapat mengunggah foto armada di bawah.');
    }

    public function edit(Vehicle $vehicle): View
    {
        $vehicle->load('images');

        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(StoreVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validated();
        $validated['requires_driver'] = $request->boolean('requires_driver', false);
        $validated['has_ac'] = $request->boolean('has_ac', true);
        $validated['is_available'] = $request->boolean('is_available', true);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Data armada kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }

    public function storeImage(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'alt_text' => ['nullable', 'string', 'max:150'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('image')->store('vehicles', 'public');

        $isPrimary = $request->boolean('is_primary');
        if ($isPrimary) {
            $vehicle->images()->update(['is_primary' => false]);
        }

        $sortOrder = (int) $vehicle->images()->max('sort_order') + 1;

        $vehicle->images()->create([
            'image_path' => $path,
            'alt_text' => $request->input('alt_text'),
            'is_primary' => $isPrimary || $vehicle->images()->count() === 0,
            'sort_order' => $sortOrder,
        ]);

        return back()->with('success', 'Foto kendaraan berhasil diunggah.');
    }

    public function destroyImage(Vehicle $vehicle, VehicleImage $image): RedirectResponse
    {
        if ($image->vehicle_id !== $vehicle->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Foto kendaraan berhasil dihapus.');
    }
}
