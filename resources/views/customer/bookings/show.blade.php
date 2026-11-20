@extends('layouts.app')

@section('title', 'Detail Booking ' . $booking->booking_code . ' — TripMate')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('customer.bookings.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali ke Riwayat Pesanan
        </a>

        @if($booking->status->value === 'pending')
            <form action="{{ route('customer.bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Batalkan booking ini?');">
                @csrf
                <button type="submit" class="px-4 py-2 border border-rose-300 text-rose-600 hover:bg-rose-50 text-xs font-semibold rounded-xl transition">
                    Batalkan Booking
                </button>
            </form>
        @endif
    </div>

    <!-- Booking Summary Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Nomor Booking</span>
                <h1 class="text-2xl font-black font-mono text-slate-900 mt-0.5">{{ $booking->booking_code }}</h1>
                <span class="text-xs text-slate-400">Dibuat pada: {{ $booking->created_at->format('d M Y, H:i') }}</span>
            </div>

            <div class="text-right">
                <span class="text-xs text-slate-400 block mb-1">Status Reservasi</span>
                @php
                    $badgeClasses = match($booking->status->value) {
                        'pending' => 'bg-amber-100 text-amber-800',
                        'confirmed' => 'bg-blue-100 text-blue-800',
                        'ongoing' => 'bg-purple-100 text-purple-800',
                        'completed' => 'bg-emerald-100 text-emerald-800',
                        'cancelled' => 'bg-rose-100 text-rose-800',
                        default => 'bg-slate-100 text-slate-700'
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badgeClasses }}">
                    {{ $booking->status->label() }}
                </span>
            </div>
        </div>

        <!-- Vehicle Info -->
        <div class="py-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-16 bg-slate-100 rounded-xl overflow-hidden flex items-center justify-center">
                    @if($booking->vehicle->primaryImage)
                        <img src="{{ asset('storage/' . $booking->vehicle->primaryImage->image_path) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl">🚗</span>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</h3>
                    <p class="text-xs text-slate-500">{{ $booking->vehicle->category->label() }} • {{ $booking->vehicle->transmission->label() }} • {{ $booking->vehicle->seats }} Kursi</p>
                </div>
            </div>
            <a href="{{ route('customer.vehicles.show', $booking->vehicle) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                Lihat Detail Unit →
            </a>
        </div>

        <!-- Rental Dates & Addresses -->
        <div class="py-6 border-b border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div>
                <span class="text-slate-400 block mb-1">WAKTU PENJEMPUTAN</span>
                <strong class="text-slate-900 text-sm block">{{ $booking->pickup_datetime->format('d M Y, H:i') }} WIB</strong>
                <p class="text-slate-600 mt-1">📍 {{ $booking->pickup_address }}</p>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">WAKTU PENGEMBALIAN</span>
                <strong class="text-slate-900 text-sm block">{{ $booking->return_datetime->format('d M Y, H:i') }} WIB</strong>
                <p class="text-slate-600 mt-1">📍 {{ $booking->return_address }}</p>
            </div>
        </div>

        <!-- Price Snapshot Table (PRD §11 & arsitektur.md §11) -->
        <div class="py-6 border-b border-slate-100">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Rincian Tarif & Tagihan (Snapshot)</h4>
            <div class="bg-slate-50 rounded-2xl p-4 text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-600">Tarif Sewa Harian Snapshot:</span>
                    <strong>Rp {{ number_format((float) $booking->daily_rate_snapshot, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Durasi Sewa:</span>
                    <strong>{{ $booking->rental_days }} Hari</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Subtotal Sewa:</span>
                    <strong>Rp {{ number_format((float) $booking->subtotal, 0, ',', '.') }}</strong>
                </div>
                @if($booking->additional_fee > 0)
                    <div class="flex justify-between text-blue-700">
                        <span>Biaya Tambahan (Layanan Sopir):</span>
                        <strong>+ Rp {{ number_format((float) $booking->additional_fee, 0, ',', '.') }}</strong>
                    </div>
                @endif
                @if($booking->discount_amount > 0)
                    <div class="flex justify-between text-emerald-700">
                        <span>Diskon:</span>
                        <strong>- Rp {{ number_format((float) $booking->discount_amount, 0, ',', '.') }}</strong>
                    </div>
                @endif
                <div class="flex justify-between text-slate-500 pt-2 border-t border-slate-200">
                    <span>Deposit Jaminan (Dikembalikan setelah sewa):</span>
                    <strong>Rp {{ number_format((float) $booking->deposit_amount, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between text-sm font-bold text-slate-900 pt-2 border-t border-slate-200">
                    <span>Total Pembayaran:</span>
                    <span class="text-base text-blue-600 font-extrabold">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Action Section: Payment or Review -->
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                @if($booking->status->value === 'pending')
                    <p class="text-xs text-amber-700 font-semibold">
                        ⏳ Menunggu pembayaran. Unit ini ditahan selama maksimal 24 jam.
                    </p>
                @elseif($booking->status->value === 'confirmed')
                    <p class="text-xs text-emerald-700 font-semibold">
                        ✅ Pembayaran telah terverifikasi. Unit telah siap untuk jadwal Anda.
                    </p>
                @elseif($booking->status->value === 'completed')
                    <p class="text-xs text-slate-600">
                        Penyewaan telah selesai. Berikan penilaian Anda untuk armada ini.
                    </p>
                @endif
            </div>

            <div class="flex items-center space-x-3">
                @if($booking->status->value === 'pending')
                    <a href="{{ route('customer.bookings.payment', $booking) }}" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow transition">
                        💳 Unggah Bukti Pembayaran
                    </a>
                @endif

                @if($booking->status->value === 'completed')
                    @if(!$booking->review)
                        <a href="{{ route('customer.bookings.review.create', $booking) }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition">
                            ★ Beri Ulasan & Rating
                        </a>
                    @else
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg">
                            ★ Ulasan Diberikan ({{ $booking->review->rating }}/5)
                        </span>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
