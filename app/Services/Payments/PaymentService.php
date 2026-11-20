<?php

namespace App\Services\Payments;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Submit payment proof by customer for a booking.
     */
    public function submitPaymentProof(Booking $booking, UploadedFile $proofFile, ?string $notes = null): Payment
    {
        if ($booking->status === BookingStatus::Cancelled) {
            throw ValidationException::withMessages([
                'booking' => 'Booking ini telah dibatalkan dan tidak dapat menerima pembayaran.',
            ]);
        }

        // Store file in public disk under payment-proofs
        $path = $proofFile->store('payment-proofs', 'public');

        return DB::transaction(function () use ($booking, $path) {
            $payment = $booking->latestPayment ?? new Payment(['booking_id' => $booking->id]);

            $payment->fill([
                'proof_path' => $path,
                'status' => PaymentStatus::Pending,
                'amount' => $booking->total_amount,
                'paid_at' => now(),
            ]);

            $payment->save();

            return $payment;
        });
    }

    /**
     * Staff/Admin verifies customer manual payment.
     */
    public function verifyPayment(Payment $payment, User $verifier): Payment
    {
        return DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => PaymentStatus::Paid,
                'paid_at' => $payment->paid_at ?? now(),
            ]);

            // Confirm the booking
            $booking = $payment->booking;
            if ($booking && $booking->status === BookingStatus::Pending) {
                $booking->update([
                    'status' => BookingStatus::Confirmed,
                ]);
            }

            return $payment;
        });
    }

    /**
     * Staff/Admin rejects customer manual payment.
     */
    public function rejectPayment(Payment $payment, User $verifier, ?string $reason = null): Payment
    {
        return DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => PaymentStatus::Failed,
            ]);

            return $payment;
        });
    }
}
