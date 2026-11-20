<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Vehicle;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $destinations = Destination::active()->latest()->take(6)->get();
        $featuredVehicles = Vehicle::active()->with(['images', 'primaryImage'])->latest()->take(6)->get();

        return view('customer.home', compact('destinations', 'featuredVehicles'));
    }
}
