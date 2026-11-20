@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya — TripMate')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Riwayat Pesanan Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar seluruh reservasi kendaraan Anda di TripMate</p>
    </div>
    <a href="{{ route('customer.vehicles.index') }}" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm hover:bg-blue-700 transition">
        + Pesan Mobil Baru
    </a>
</div>

<div class="space-y-4">
    @forelse($bookings as $b)
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-2">
                <div class="flex items-center space-x-3">
                    <span class="font-mono font-bold text-sm text-blue-600">{{ $b->booking_code }}</span>
                    @php
                        $badgeClasses = match($b->status->value) {
                            'pending' => 'bg-amber-100 text-amber-800',
                            'confirmed' => 'bg-blue-100 text-blue-800',
                            'ongoing' => 'bg-purple-100 text-purple-800',
                            'completed' => 'bg-emerald-100 text-emerald-800',
                            'cancelled' => 'bg-rose-100 text-rose-800',
                            default => 'bg-slate-100 text-slate-700'
                        };
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClasses }}">
                        {{ $b->status->label() }}
                    </span>
                </div>

                <h3 class="text-lg font-bold text-slate-900">
                    {{ $b->vehicle->brand }} {{ $b->vehicle->model }}
                </h3>

                <div class="flex flex-wrap gap-4 text-xs text-slate-500">
                    <span>📅 {{ $b->pickup_datetime->format('d M Y H:i') }} s/d {{ $b->return_datetime->format('d M Y H:i') }}</span>
                    <span>⏱️ {{ $b->rental_days }} Hari</span>
                    <span>💰 Total: <strong class="text-slate-800 font-bold">Rp {{ number_format((float) $b->total_amount, 0, ',', '.') }}</strong></span>
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-4 sm:pt-0 border-t sm:border-0 border-slate-100">
                <a href="{{ route('customer.bookings.show', $b) }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-semibold transition">
                    Lihat Rincian
                </a>

                @if($b->status->value === 'pending')
                    <a href="{{ route('customer.bookings.payment', $b) }}" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg text-xs font-semibold shadow transition">
                        Bayar Sekarang
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-12 bg-white rounded-2xl border border-slate-200">
            <span class="text-4xl text-slate-300 block mb-2">📋</span>
            <p class="text-slate-600 font-semibold mb-1">Belum ada riwayat pesanan.</p>
            <p class="text-xs text-slate-400 mb-6">Pilih kendaraan atau gunakan rekomendasi untuk mulai menyewa.</p>
            <a href="{{ route('customer.vehicles.index') }}" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm hover:bg-blue-700 transition">
                Jelajahi Mobil Sekarang
            </a>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $bookings->links() }}
</div>
@endsection
