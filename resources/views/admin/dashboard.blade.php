@extends('layouts.admin')

@section('title', 'Dashboard Operasional — TripMate')
@section('page_title', 'Ringkasan Operasional')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Armada Mobil</span>
        <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['total_vehicles'] }}</div>
        <span class="text-xs text-emerald-600 font-semibold mt-1 block">{{ $stats['active_vehicles'] }} Unit Aktif Disewakan</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Booking Menunggu Bayar</span>
        <div class="text-3xl font-extrabold text-amber-600 mt-2">{{ $stats['pending_bookings'] }}</div>
        <span class="text-xs text-slate-400 mt-1 block">Hold 24 jam</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Verifikasi Pembayaran</span>
        <div class="text-3xl font-extrabold text-blue-600 mt-2">{{ $stats['pending_payments'] }}</div>
        <span class="text-xs text-slate-400 mt-1 block">Menunggu verifikasi bukti transfer</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Pendapatan Terverifikasi</span>
        <div class="text-2xl font-extrabold text-emerald-600 mt-2">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
        <span class="text-xs text-slate-400 mt-1 block">Dari transaksi status Lunas</span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Pending Payments Queue -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Perlu Verifikasi Pembayaran</h3>
            <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-blue-600 hover:underline">Semua →</a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($pendingPayments as $p)
                <div class="py-3 flex items-center justify-between text-xs">
                    <div>
                        <strong class="text-slate-900 block font-mono">{{ $p->booking->booking_code }}</strong>
                        <span class="text-slate-500">{{ $p->booking->user->name }} • {{ $p->booking->vehicle->brand }} {{ $p->booking->vehicle->model }}</span>
                        <span class="block text-blue-600 font-bold mt-0.5">Rp {{ number_format((float) $p->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($p->proof_path)
                            <a href="{{ asset('storage/' . $p->proof_path) }}" target="_blank" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg">
                                Bukti
                            </a>
                        @endif
                        <form action="{{ route('admin.payments.verify', $p) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg">
                                Verifikasi
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-6 text-center">Tidak ada pembayaran yang menunggu verifikasi.</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Booking Terbaru</h3>
            <a href="{{ route('admin.bookings.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Semua →</a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($recentBookings as $b)
                <div class="py-3 flex items-center justify-between text-xs">
                    <div>
                        <div class="flex items-center space-x-2">
                            <strong class="text-slate-900 font-mono">{{ $b->booking_code }}</strong>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $b->status->value === 'confirmed' ? 'bg-blue-100 text-blue-800' : ($b->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                {{ $b->status->label() }}
                            </span>
                        </div>
                        <span class="text-slate-500">{{ $b->user->name }} • {{ $b->vehicle->brand }} {{ $b->vehicle->model }}</span>
                        <span class="text-slate-400 block">{{ $b->pickup_datetime->format('d M') }} - {{ $b->return_datetime->format('d M Y') }}</span>
                    </div>
                    <a href="{{ route('admin.bookings.show', $b) }}" class="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold rounded-lg">
                        Detail
                    </a>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-6 text-center">Belum ada transaksi booking.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
