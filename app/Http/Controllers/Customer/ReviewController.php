<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Booking $booking): View
    {
        Gate::authorize('create', [Review::class, $booking]);

        $booking->load('vehicle');

        return view('customer.reviews.create', compact('booking'));
    }

    public function store(StoreReviewRequest $request, Booking $booking): RedirectResponse
    {
        Gate::authorize('create', [Review::class, $booking]);

        Review::create([
            'booking_id' => $booking->id,
            'user_id' => $request->user()->id,
            'vehicle_id' => $booking->vehicle_id,
            'rating' => (int) $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Terima kasih atas ulasan dan penilaian Anda!');
    }
}
