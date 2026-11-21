@extends('layouts.app')

@section('title', 'Detail Booking ' . $booking->booking_code . ' — TripMate')

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
<div class="max-w-5xl mx-auto space-y-6">
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
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
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
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold {{ $badgeClasses }}">
                    {{ $booking->status->label() }}
                </span>
            </div>
        </div>

        <!-- Vehicle Info -->
        <div class="py-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-16 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center">
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

        <!-- Rental Dates & Indonesian Administrative Addresses -->
        <div class="py-6 border-b border-slate-100 space-y-6">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jadwal & Lokasi Serah Terima Kendaraan</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <!-- Penjemputan -->
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-[11px] uppercase tracking-wider">
                            📍 Titik Penjemputan
                        </span>
                        @if($booking->has_pickup_coordinates)
                            <a href="{{ $booking->pickup_maps_url }}" target="_blank" class="text-blue-600 font-semibold hover:underline flex items-center gap-1 text-[11px]">
                                Buka di Google Maps ↗
                            </a>
                        @endif
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">WAKTU</span>
                        <strong class="text-slate-900 text-sm block">{{ $booking->pickup_datetime->format('d M Y, H:i') }} WIB</strong>
                    </div>
                    <div class="pt-1 border-t border-slate-200/60">
                        <span class="text-slate-400 block text-[11px]">ALAMAT LENGKAP</span>
                        <p class="text-slate-800 font-medium leading-relaxed mt-0.5">
                            {{ $booking->pickup_address }}
                        </p>
                        @if($booking->pickup_rt_rw || $booking->pickupVillage || $booking->pickupDistrict || $booking->pickupRegency || $booking->pickupProvince)
                            <p class="text-slate-500 text-[11px] mt-1 leading-relaxed">
                                @if($booking->pickup_rt_rw)<span class="mr-2">{{ $booking->pickup_rt_rw }}</span>@endif
                                @if($booking->pickupVillage)<span>Kel. {{ $booking->pickupVillage->name }}, </span>@endif
                                @if($booking->pickupDistrict)<span>Kec. {{ $booking->pickupDistrict->name }}, </span>@endif
                                @if($booking->pickupRegency)<span>{{ $booking->pickupRegency->name }}, </span>@endif
                                @if($booking->pickupProvince)<span>{{ $booking->pickupProvince->name }}</span>@endif
                                @if($booking->pickup_postal_code)<span> {{ $booking->pickup_postal_code }}</span>@endif
                            </p>
                        @endif
                        @if($booking->has_pickup_coordinates)
                            <span class="inline-block mt-2 font-mono text-[10px] bg-white border border-slate-200 px-2 py-0.5 rounded text-slate-500">
                                Lat: {{ $booking->pickup_latitude }}, Lng: {{ $booking->pickup_longitude }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Pengembalian -->
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-lg font-bold text-[11px] uppercase tracking-wider">
                            🏁 Titik Pengembalian
                        </span>
                        @if($booking->has_return_coordinates)
                            <a href="{{ $booking->return_maps_url }}" target="_blank" class="text-blue-600 font-semibold hover:underline flex items-center gap-1 text-[11px]">
                                Buka di Google Maps ↗
                            </a>
                        @endif
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">WAKTU</span>
                        <strong class="text-slate-900 text-sm block">{{ $booking->return_datetime->format('d M Y, H:i') }} WIB</strong>
                    </div>
                    <div class="pt-1 border-t border-slate-200/60">
                        <span class="text-slate-400 block text-[11px]">ALAMAT LENGKAP</span>
                        <p class="text-slate-800 font-medium leading-relaxed mt-0.5">
                            {{ $booking->return_address }}
                        </p>
                        @if($booking->return_rt_rw || $booking->returnVillage || $booking->returnDistrict || $booking->returnRegency || $booking->returnProvince)
                            <p class="text-slate-500 text-[11px] mt-1 leading-relaxed">
                                @if($booking->return_rt_rw)<span class="mr-2">{{ $booking->return_rt_rw }}</span>@endif
                                @if($booking->returnVillage)<span>Kel. {{ $booking->returnVillage->name }}, </span>@endif
                                @if($booking->returnDistrict)<span>Kec. {{ $booking->returnDistrict->name }}, </span>@endif
                                @if($booking->returnRegency)<span>{{ $booking->returnRegency->name }}, </span>@endif
                                @if($booking->returnProvince)<span>{{ $booking->returnProvince->name }}</span>@endif
                                @if($booking->return_postal_code)<span> {{ $booking->return_postal_code }}</span>@endif
                            </p>
                        @endif
                        @if($booking->has_return_coordinates)
                            <span class="inline-block mt-2 font-mono text-[10px] bg-white border border-slate-200 px-2 py-0.5 rounded text-slate-500">
                                Lat: {{ $booking->return_latitude }}, Lng: {{ $booking->return_longitude }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Interactive Map Display -->
            @if($booking->has_pickup_coordinates || $booking->has_return_coordinates)
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-700">Visualisasi Peta Serah Terima:</span>
                        <span class="text-[11px] text-slate-400">Hijau: Penjemputan • Biru: Pengembalian</span>
                    </div>
                    <div id="booking-detail-map" class="h-64 sm:h-80 w-full rounded-2xl border border-slate-300 shadow-inner"></div>
                </div>
            @endif
        </div>

        <!-- Price Snapshot Table (Guaranteed whitespace-nowrap) -->
        <div class="py-6 border-b border-slate-100">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Rincian Tarif & Tagihan (Snapshot)</h4>
            <div class="bg-slate-50 rounded-2xl p-5 text-xs space-y-3 border border-slate-100">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-slate-600">Tarif Sewa Harian Snapshot:</span>
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
                    <span class="text-slate-600">Subtotal Sewa:</span>
                    <strong class="text-slate-800 whitespace-nowrap shrink-0 text-right">
                        Rp {{ number_format((float) $booking->subtotal, 0, ',', '.') }}
                    </strong>
                </div>
                @if($booking->additional_fee > 0)
                    <div class="flex items-center justify-between gap-2 text-blue-700">
                        <span>Biaya Tambahan (Layanan Sopir):</span>
                        <strong class="whitespace-nowrap shrink-0 text-right">
                            + Rp {{ number_format((float) $booking->additional_fee, 0, ',', '.') }}
                        </strong>
                    </div>
                @endif
                @if($booking->discount_amount > 0)
                    <div class="flex items-center justify-between gap-2 text-emerald-700">
                        <span>Diskon:</span>
                        <strong class="whitespace-nowrap shrink-0 text-right">
                            - Rp {{ number_format((float) $booking->discount_amount, 0, ',', '.') }}
                        </strong>
                    </div>
                @endif
                <div class="flex items-center justify-between gap-2 text-slate-500 pt-2 border-t border-slate-200">
                    <span>Deposit Jaminan (Dikembalikan setelah sewa):</span>
                    <strong class="text-slate-700 whitespace-nowrap shrink-0 text-right">
                        Rp {{ number_format((float) $booking->deposit_amount, 0, ',', '.') }}
                    </strong>
                </div>
                <div class="flex items-baseline justify-between gap-3 text-sm font-bold text-slate-900 pt-2.5 border-t border-slate-200">
                    <span class="whitespace-nowrap">Total Pembayaran:</span>
                    <span class="text-xl font-black text-blue-600 whitespace-nowrap shrink-0 text-right">
                        Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}
                    </span>
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
                @elseif($booking->status->value === 'ongoing')
                    <p class="text-xs text-purple-700 font-semibold">
                        🚗 Kendaraan sedang dalam masa sewa aktif. Selamat menikmati perjalanan Anda!
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
    const map = L.map('booking-detail-map').setView(mapCenter, 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const bounds = [];

    if (pickupLat && pickupLng) {
        const pickupMarker = L.marker([pickupLat, pickupLng]).addTo(map)
            .bindPopup('<strong>📍 Penjemputan</strong><br>{{ addslashes($booking->pickup_address) }}');
        bounds.push([pickupLat, pickupLng]);
    }

    if (returnLat && returnLng) {
        const returnMarker = L.marker([returnLat, returnLng]).addTo(map)
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
