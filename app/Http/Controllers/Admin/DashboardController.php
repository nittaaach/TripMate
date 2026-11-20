<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Payment;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::where('status', VehicleStatus::Active)->count(),
            'total_destinations' => Destination::count(),
            'pending_bookings' => Booking::where('status', BookingStatus::Pending)->count(),
            'active_bookings' => Booking::where('status', BookingStatus::Ongoing)->count(),
            'pending_payments' => Payment::where('status', PaymentStatus::Pending)->count(),
            'total_revenue' => (float) Payment::where('status', PaymentStatus::Paid)->sum('amount'),
        ];

        $recentBookings = Booking::with(['user', 'vehicle'])
            ->latest()
            ->take(5)
            ->get();

        $pendingPayments = Payment::with(['booking.user', 'booking.vehicle'])
            ->where('status', PaymentStatus::Pending)
            ->whereNotNull('proof_path')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'pendingPayments'));
    }
}
