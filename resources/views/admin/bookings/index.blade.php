@extends('layouts.admin')

@section('title', 'Kelola Reservasi Booking — Admin TripMate')
@section('page_title', 'Kelola Reservasi Booking Pelanggan')

@section('content')
<!-- Filter bar -->
<div class="bg-white p-4 rounded-2xl border border-slate-200 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <form action="{{ route('admin.bookings.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode Booking / Nama Customer..."
            class="px-3.5 py-2 border border-slate-300 rounded-xl text-xs w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">

        <select name="status" class="px-3 py-2 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            @foreach(\App\Enums\BookingStatus::cases() as $st)
                <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
            @endforeach
        </select>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold text-xs rounded-xl hover:bg-blue-700 transition">
            Filter
        </button>
    </form>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse text-xs">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                <th class="p-4">Kode & Customer</th>
                <th class="p-4">Armada Mobil</th>
                <th class="p-4">Rentang Waktu Sewa</th>
                <th class="p-4">Total Biaya</th>
                <th class="p-4">Status Booking</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($bookings as $b)
                <tr class="hover:bg-slate-50/50">
                    <td class="p-4">
                        <strong class="text-sm font-mono text-blue-600 block">{{ $b->booking_code }}</strong>
                        <span class="text-slate-800 font-semibold">{{ $b->user->name }}</span>
                        <span class="text-slate-400 block text-[11px]">{{ $b->user->phone ?? $b->user->email }}</span>
                    </td>
                    <td class="p-4">
                        <strong class="text-slate-900 block">{{ $b->vehicle->brand }} {{ $b->vehicle->model }}</strong>
                        <span class="text-slate-400 font-mono text-[11px]">{{ $b->vehicle->vehicle_code }}</span>
                    </td>
                    <td class="p-4 text-slate-600">
                        <div>📅 {{ $b->pickup_datetime->format('d M Y, H:i') }}</div>
                        <div>🏁 {{ $b->return_datetime->format('d M Y, H:i') }}</div>
                        <span class="text-slate-400 text-[10px] block mt-0.5">Durasi: {{ $b->rental_days }} Hari</span>
                    </td>
                    <td class="p-4">
                        <strong class="text-slate-900 text-sm block">Rp {{ number_format((float) $b->total_amount, 0, ',', '.') }}</strong>
                        <span class="text-[10px] text-slate-400">Termasuk Deposit</span>
                    </td>
                    <td class="p-4">
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
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $badgeClasses }}">
                            {{ $b->status->label() }}
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <a href="{{ route('admin.bookings.show', $b) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg">
                            Kelola Status →
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada data transaksi booking.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4 border-t border-slate-100">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
