<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitPaymentProofRequest;
use App\Models\Booking;
use App\Services\Payments\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {}

    public function show(Booking $booking): View
    {
        Gate::authorize('view', $booking);

        $booking->load(['vehicle', 'latestPayment']);

        return view('customer.payments.show', compact('booking'));
    }

    public function store(SubmitPaymentProofRequest $request, Booking $booking): RedirectResponse
    {
        Gate::authorize('view', $booking);

        $this->paymentService->submitPaymentProof(
            $booking,
            $request->file('proof_file'),
            $request->input('notes'),
        );

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Bukti transfer berhasil diunggah! Tim kami akan segera memverifikasi pembayaran Anda.');
    }
}
