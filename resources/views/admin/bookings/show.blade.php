@extends('layouts.admin')

@section('title', 'Detail Booking ' . $booking->booking_code . ' — Admin TripMate')
@section('page_title', 'Detail Booking & Pembaruan Status: ' . $booking->booking_code)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-container {
        z-index: 10 !important;
        font-family: inherit;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl space-y-6">
    <div class="mb-4">
        <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali ke Daftar Booking
        </a>
    </div>

    <!-- Main Detail & Status Update -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
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
                <button type="submit" class="px-3.5 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition">
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

        <!-- Schedule & Detailed Location -->
        <div class="py-6 border-b border-slate-100 space-y-4">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jadwal & Lokasi Serah Terima Kendaraan</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <!-- Penjemputan -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold text-[11px] uppercase">
                            📍 Penjemputan
                        </span>
                        @if($booking->has_pickup_coordinates)
                            <a href="{{ $booking->pickup_maps_url }}" target="_blank" class="text-blue-600 font-semibold hover:underline text-[11px]">
                                Google Maps ↗
                            </a>
                        @endif
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">WAKTU</span>
                        <strong class="text-slate-900 block text-sm">{{ $booking->pickup_datetime->format('d M Y, H:i') }} WIB</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">ALAMAT LENGKAP</span>
                        <p class="text-slate-800 font-medium leading-relaxed">{{ $booking->pickup_address }}</p>
                        @if($booking->pickup_rt_rw || $booking->pickupVillage || $booking->pickupDistrict || $booking->pickupRegency || $booking->pickupProvince)
                            <p class="text-slate-500 text-[11px] mt-1 leading-relaxed">
                                @if($booking->pickup_rt_rw)<span class="mr-1">{{ $booking->pickup_rt_rw }},</span>@endif
                                @if($booking->pickupVillage)<span>Kel. {{ $booking->pickupVillage->name }},</span>@endif
                                @if($booking->pickupDistrict)<span>Kec. {{ $booking->pickupDistrict->name }},</span>@endif
                                @if($booking->pickupRegency)<span>{{ $booking->pickupRegency->name }},</span>@endif
                                @if($booking->pickupProvince)<span>{{ $booking->pickupProvince->name }}</span>@endif
                                @if($booking->pickup_postal_code)<span> {{ $booking->pickup_postal_code }}</span>@endif
                            </p>
                        @endif
                        @if($booking->has_pickup_coordinates)
                            <span class="inline-block mt-1 font-mono text-[10px] bg-white border border-slate-200 px-2 py-0.5 rounded text-slate-500">
                                Lat: {{ $booking->pickup_latitude }}, Lng: {{ $booking->pickup_longitude }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Pengembalian -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-bold text-[11px] uppercase">
                            🏁 Pengembalian
                        </span>
                        @if($booking->has_return_coordinates)
                            <a href="{{ $booking->return_maps_url }}" target="_blank" class="text-blue-600 font-semibold hover:underline text-[11px]">
                                Google Maps ↗
                            </a>
                        @endif
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">WAKTU</span>
                        <strong class="text-slate-900 block text-sm">{{ $booking->return_datetime->format('d M Y, H:i') }} WIB</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">ALAMAT LENGKAP</span>
                        <p class="text-slate-800 font-medium leading-relaxed">{{ $booking->return_address }}</p>
                        @if($booking->return_rt_rw || $booking->returnVillage || $booking->returnDistrict || $booking->returnRegency || $booking->returnProvince)
                            <p class="text-slate-500 text-[11px] mt-1 leading-relaxed">
                                @if($booking->return_rt_rw)<span class="mr-1">{{ $booking->return_rt_rw }},</span>@endif
                                @if($booking->returnVillage)<span>Kel. {{ $booking->returnVillage->name }},</span>@endif
                                @if($booking->returnDistrict)<span>Kec. {{ $booking->returnDistrict->name }},</span>@endif
                                @if($booking->returnRegency)<span>{{ $booking->returnRegency->name }},</span>@endif
                                @if($booking->returnProvince)<span>{{ $booking->returnProvince->name }}</span>@endif
                                @if($booking->return_postal_code)<span> {{ $booking->return_postal_code }}</span>@endif
                            </p>
                        @endif
                        @if($booking->has_return_coordinates)
                            <span class="inline-block mt-1 font-mono text-[10px] bg-white border border-slate-200 px-2 py-0.5 rounded text-slate-500">
                                Lat: {{ $booking->return_latitude }}, Lng: {{ $booking->return_longitude }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Admin Map Container -->
            @if($booking->has_pickup_coordinates || $booking->has_return_coordinates)
                <div class="pt-2">
                    <div id="admin-booking-map" class="h-64 w-full rounded-2xl border border-slate-300 shadow-inner"></div>
                </div>
            @endif
        </div>

        <!-- Snapshot Table (Guaranteed whitespace-nowrap) -->
        <div class="py-6 border-b border-slate-100">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Snapshot Tarif Transaksi</h4>
            <div class="bg-slate-50 rounded-2xl p-4 text-xs space-y-2.5 border border-slate-100">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-slate-600">Tarif Sewa Harian:</span>
                    <strong class="text-slate-800 whitespace-nowrap shrink-0 text-right">
                        Rp {{ number_format((float) $booking->daily_rate_snapshot, 0, ',', '.') }}
                    </strong>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-slate-600">Durasi Sewa:</span>
                    <strong class="text-slate-800 whitespace-nowrap shrink-0 text-right">
                        {{ $booking->rental_days }} Hari
                    </strong>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-slate-600">Subtotal:</span>
                    <strong class="text-slate-800 whitespace-nowrap shrink-0 text-right">
                        Rp {{ number_format((float) $booking->subtotal, 0, ',', '.') }}
                    </strong>
                </div>
                @if($booking->additional_fee > 0)
                    <div class="flex items-center justify-between gap-2 text-blue-700">
                        <span>Biaya Tambahan (Sopir):</span>
                        <strong class="whitespace-nowrap shrink-0 text-right">
                            + Rp {{ number_format((float) $booking->additional_fee, 0, ',', '.') }}
                        </strong>
                    </div>
                @endif
                <div class="flex items-center justify-between gap-2 text-slate-500 pt-2 border-t border-slate-200">
                    <span>Deposit Jaminan:</span>
                    <strong class="text-slate-700 whitespace-nowrap shrink-0 text-right">
                        Rp {{ number_format((float) $booking->deposit_amount, 0, ',', '.') }}
                    </strong>
                </div>
                <div class="flex items-baseline justify-between gap-3 text-sm font-bold text-slate-900 pt-2.5 border-t border-slate-200">
                    <span class="whitespace-nowrap">Total Pembayaran:</span>
                    <strong class="text-base text-blue-600 whitespace-nowrap shrink-0 text-right">
                        Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}
                    </strong>
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
                            <strong class="text-slate-900 block whitespace-nowrap">Rp {{ number_format((float) $pay->amount, 0, ',', '.') }} ({{ $pay->payment_method->label() }})</strong>
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

@push('scripts')
@if($booking->has_pickup_coordinates || $booking->has_return_coordinates)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pickupLat = {{ $booking->pickup_latitude ?? 'null' }};
    const pickupLng = {{ $booking->pickup_longitude ?? 'null' }};
    const returnLat = {{ $booking->return_latitude ?? 'null' }};
    const returnLng = {{ $booking->return_longitude ?? 'null' }};

    const mapCenter = pickupLat ? [pickupLat, pickupLng] : [returnLat, returnLng];
    const map = L.map('admin-booking-map').setView(mapCenter, 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const bounds = [];

    if (pickupLat && pickupLng) {
        L.marker([pickupLat, pickupLng]).addTo(map)
            .bindPopup('<strong>📍 Penjemputan</strong><br>{{ addslashes($booking->pickup_address) }}');
        bounds.push([pickupLat, pickupLng]);
    }

    if (returnLat && returnLng) {
        L.marker([returnLat, returnLng]).addTo(map)
            .bindPopup('<strong>🏁 Pengembalian</strong><br>{{ addslashes($booking->return_address) }}');
        bounds.push([returnLat, returnLng]);
    }

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }
});
</script>
@endif
@endpush
