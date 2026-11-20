@extends('layouts.admin')

@section('title', 'Detail Booking ' . $booking->booking_code . ' — Admin TripMate')
@section('page_title', 'Detail Booking & Pembaruan Status: ' . $booking->booking_code)

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="mb-4">
        <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali ke Daftar Booking
        </a>
    </div>

    <!-- Main Detail & Status Update -->
    <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Kode Booking</span>
                <h2 class="text-2xl font-black font-mono text-slate-900">{{ $booking->booking_code }}</h2>
                <span class="text-xs text-slate-500">Dibuat pada: {{ $booking->created_at->format('d M Y, H:i') }} WIB</span>
            </div>

            <!-- Status Changer Form -->
            <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST" class="flex items-center space-x-3 bg-slate-50 p-3 rounded-2xl border border-slate-200">
                @csrf
                @method('PATCH')
                <label for="status" class="text-xs font-bold text-slate-700">Ubah Status:</label>
                <select name="status" id="status" class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Enums\BookingStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ $booking->status === $st ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition">
                    Simpan
                </button>
            </form>
        </div>

        <!-- Customer & Vehicle Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block mb-2">Informasi Pelanggan</span>
                <strong class="text-slate-900 text-sm block">{{ $booking->user->name }}</strong>
                <p class="text-slate-600 mt-0.5">Email: {{ $booking->user->email }}</p>
                <p class="text-slate-600">Telepon: {{ $booking->user->phone ?? '—' }}</p>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block mb-2">Armada Mobil</span>
                <strong class="text-slate-900 text-sm block">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</strong>
                <p class="text-slate-600 mt-0.5">Kode: {{ $booking->vehicle->vehicle_code }} • {{ $booking->vehicle->category->label() }}</p>
                <p class="text-slate-600">{{ $booking->vehicle->seats }} Kursi • Transmisi: {{ $booking->vehicle->transmission->label() }}</p>
            </div>
        </div>

        <!-- Schedule & Location -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block mb-1">Jadwal & Lokasi Penjemputan</span>
                <strong class="text-slate-900 block text-sm">{{ $booking->pickup_datetime->format('d M Y, H:i') }} WIB</strong>
                <p class="text-slate-600 mt-1">📍 {{ $booking->pickup_address }}</p>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block mb-1">Jadwal & Lokasi Pengembalian</span>
                <strong class="text-slate-900 block text-sm">{{ $booking->return_datetime->format('d M Y, H:i') }} WIB</strong>
                <p class="text-slate-600 mt-1">📍 {{ $booking->return_address }}</p>
            </div>
        </div>

        <!-- Snapshot Table -->
        <div class="py-6 border-b border-slate-100">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Snapshot Tarif Transaksi</h4>
            <div class="bg-slate-50 rounded-2xl p-4 text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-600">Tarif Sewa Harian:</span>
                    <strong>Rp {{ number_format((float) $booking->daily_rate_snapshot, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Durasi Sewa:</span>
                    <strong>{{ $booking->rental_days }} Hari</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Subtotal:</span>
                    <strong>Rp {{ number_format((float) $booking->subtotal, 0, ',', '.') }}</strong>
                </div>
                @if($booking->additional_fee > 0)
                    <div class="flex justify-between text-blue-700">
                        <span>Biaya Tambahan (Sopir):</span>
                        <strong>+ Rp {{ number_format((float) $booking->additional_fee, 0, ',', '.') }}</strong>
                    </div>
                @endif
                <div class="flex justify-between text-slate-500 pt-2 border-t border-slate-200">
                    <span>Deposit Jaminan:</span>
                    <strong>Rp {{ number_format((float) $booking->deposit_amount, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between text-sm font-bold text-slate-900 pt-2 border-t border-slate-200">
                    <span>Total Pembayaran:</span>
                    <strong class="text-base text-blue-600">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="pt-6">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Riwayat Pembayaran Booking Ini</h4>
            <div class="space-y-3 text-xs">
                @forelse($booking->payments as $pay)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <strong class="text-slate-900 block">Rp {{ number_format((float) $pay->amount, 0, ',', '.') }} ({{ $pay->payment_method->label() }})</strong>
                            <span class="text-slate-500 text-[11px]">Waktu Bayar: {{ $pay->paid_at ? $pay->paid_at->format('d M Y, H:i') : 'Menunggu upload' }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-0.5 rounded-full font-bold {{ $pay->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $pay->status->label() }}
                            </span>
                            @if($pay->proof_path)
                                <a href="{{ asset('storage/' . $pay->proof_path) }}" target="_blank" class="px-3 py-1 bg-white border border-slate-300 rounded font-semibold text-slate-700 hover:bg-slate-100">
                                    Lihat Bukti
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 text-xs">Belum ada catatan pembayaran.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
