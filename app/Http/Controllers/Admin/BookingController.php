<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with(['user', 'vehicle', 'latestPayment'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'user',
            'vehicle.images',
            'payments',
            'vacationPlan.destination',
            'review',
            'pickupProvince',
            'pickupRegency',
            'pickupDistrict',
            'pickupVillage',
            'returnProvince',
            'returnRegency',
            'returnDistrict',
            'returnVillage',
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,ongoing,completed,cancelled'],
        ]);

        $booking->update([
            'status' => BookingStatus::from($validated['status']),
        ]);

        return back()->with('success', 'Status booking berhasil diubah menjadi: ' . $booking->status->label());
    }
}
