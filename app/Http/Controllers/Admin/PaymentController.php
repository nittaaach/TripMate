<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payments\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {}

    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with(['booking.user', 'booking.vehicle'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['booking.user', 'booking.vehicle']);

        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        $this->paymentService->verifyPayment($payment, $request->user());

        return back()->with('success', 'Pembayaran berhasil diverifikasi! Status booking otomatis menjadi Terkonfirmasi.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $reason = $request->input('reason');
        $this->paymentService->rejectPayment($payment, $request->user(), $reason);

        return back()->with('success', 'Pembayaran ditolak / ditandai gagal.');
    }
}
