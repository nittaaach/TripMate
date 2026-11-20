<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Destination::active();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('province', 'like', "%{$search}%");
            });
        }

        if ($request->filled('terrain_type')) {
            $query->where('terrain_type', $request->input('terrain_type'));
        }

        $destinations = $query->paginate(12)->withQueryString();

        return view('customer.destinations.index', compact('destinations'));
    }

    public function show(string $slug): View
    {
        $destination = Destination::where('slug', $slug)->firstOrFail();

        return view('customer.destinations.show', compact('destination'));
    }
}
