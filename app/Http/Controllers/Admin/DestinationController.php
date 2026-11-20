<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDestinationRequest;
use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(Request $request): View
    {
        $destinations = Destination::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('city', 'like', '%' . $request->input('search') . '%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.destinations.index', compact('destinations'));
    }

    public function create(): View
    {
        return view('admin.destinations.create');
    }

    public function store(StoreDestinationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['is_active'] = $request->boolean('is_active', true);

        Destination::create($validated);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi wisata berhasil ditambahkan.');
    }

    public function edit(Destination $destination): View
    {
        return view('admin.destinations.edit', compact('destination'));
    }

    public function update(StoreDestinationRequest $request, Destination $destination): RedirectResponse
    {
        $validated = $request->validated();
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['is_active'] = $request->boolean('is_active', true);

        $destination->update($validated);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi wisata berhasil diperbarui.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        $destination->delete();

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi wisata berhasil dihapus.');
    }
}
