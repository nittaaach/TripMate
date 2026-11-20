@extends('layouts.app')

@section('title', 'Pembayaran Booking ' . $booking->booking_code . ' — TripMate')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('customer.bookings.show', $booking) }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali ke Detail Booking
        </a>
    </div>

    <!-- Instruction Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
        <div class="text-center pb-6 border-b border-slate-100">
            <span class="text-xs uppercase font-bold text-blue-600 tracking-wider">Instruksi Pembayaran Manual</span>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Transfer Bank</h1>
            <p class="text-xs text-slate-500 mt-1">Silakan transfer nominal berikut tepat hingga rupiah terakhir</p>

            <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-2xl">
                <span class="text-xs text-blue-700 block mb-0.5">Total yang Harus Ditransfer</span>
                <span class="text-3xl font-black text-blue-700">
                    Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-blue-500 block mt-1">Nomor Booking: <strong>{{ $booking->booking_code }}</strong></span>
            </div>
        </div>

        <!-- Bank Accounts -->
        <div class="py-6 border-b border-slate-100 space-y-3">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilihan Rekening Resmi TripMate:</h3>

            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                <div>
                    <strong class="text-sm text-slate-900 block">Bank Central Asia (BCA)</strong>
                    <span class="text-xs text-slate-500">a.n. PT TripMate Rental Nusantara</span>
                </div>
                <div class="text-right">
                    <span class="font-mono text-sm font-bold text-slate-800">8012 3456 78</span>
                </div>
            </div>

            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                <div>
                    <strong class="text-sm text-slate-900 block">Bank Mandiri</strong>
                    <span class="text-xs text-slate-500">a.n. PT TripMate Rental Nusantara</span>
                </div>
                <div class="text-right">
                    <span class="font-mono text-sm font-bold text-slate-800">137 00 9876 5432</span>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="pt-6">
            <h3 class="text-sm font-bold text-slate-900 mb-2">Unggah Bukti Transfer</h3>
            <p class="text-xs text-slate-500 mb-4">Pastikan nominal, tanggal, dan nama pengirim terlihat jelas pada foto bukti transfer.</p>

            @if($booking->latestPayment?->proof_path)
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl text-xs text-emerald-800 mb-6 flex items-center justify-between">
                    <div>
                        <strong class="block mb-0.5">Bukti transfer sudah diunggah sebelumnya</strong>
                        <span>Status: <strong>{{ $booking->latestPayment->status->label() }}</strong></span>
                    </div>
                    <a href="{{ asset('storage/' . $booking->latestPayment->proof_path) }}" target="_blank" class="px-3 py-1 bg-white border border-emerald-300 rounded text-xs font-semibold hover:bg-emerald-100">
                        Lihat Bukti
                    </a>
                </div>
            @endif

            <form action="{{ route('customer.bookings.payment.store', $booking) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="proof_file" class="block text-xs font-bold text-slate-700 mb-1">
                        Pilih Foto Bukti Transfer (JPG, PNG, WEBP, Maks 3MB) <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="proof_file" id="proof_file" required accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-300 rounded-xl p-1">
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold text-slate-700 mb-1">Catatan Pengirim (Opsional)</label>
                    <input type="text" name="notes" id="notes" placeholder="Contoh: Transfer dari rekening BCA a.n. Budi"
                        class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm shadow-md transition">
                    Kirim Bukti Pembayaran
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
