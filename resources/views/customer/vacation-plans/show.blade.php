@extends('layouts.app')

@section('title', 'Detail Rencana Liburan — TripMate')

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
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <a href="{{ route('customer.vacation-plans.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali ke Daftar Rencana
        </a>
        <a href="{{ route('customer.vacation-plans.recommendations', $vacationPlan) }}" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold shadow hover:bg-blue-700 transition self-start sm:self-auto">
            Lihat Rekomendasi Mobil ({{ $vacationPlan->recommendations->count() }}) →
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs text-blue-600 font-semibold uppercase tracking-wider block">Destinasi Liburan</span>
                <h1 class="text-2xl font-bold text-slate-900 mt-0.5">{{ $vacationPlan->destination->name }}</h1>
                <span class="text-xs text-slate-500">📍 {{ $vacationPlan->destination->city }}, {{ $vacationPlan->destination->province }}</span>
            </div>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                {{ $vacationPlan->status->label() }}
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 border-b border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block mb-0.5">Mulai Sewa:</span>
                <strong class="text-slate-800 text-sm">{{ $vacationPlan->start_date->format('d M Y') }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Selesai Sewa:</span>
                <strong class="text-slate-800 text-sm">{{ $vacationPlan->end_date->format('d M Y') }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Jumlah Penumpang:</span>
                <strong class="text-slate-800 text-sm">{{ $vacationPlan->passenger_count }} Orang</strong>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Layanan Sopir:</span>
                <strong class="text-slate-800 text-sm">{{ $vacationPlan->needs_driver ? 'Ya (+ Sopir)' : 'Lepas Kunci' }}</strong>
            </div>
        </div>

        <div class="py-6 border-b border-slate-100 space-y-4 text-xs">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <div>
                    <strong class="text-slate-800 block mb-0.5">Titik Penjemputan / Awal:</strong>
                    <p class="text-slate-600 leading-relaxed">{{ $vacationPlan->origin_address }}</p>
                </div>
                @if($vacationPlan->estimated_distance_km)
                    <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between">
                        <span class="text-slate-500">Estimasi Jarak Rute Menuju Destinasi:</span>
                        <strong class="text-slate-900 font-bold whitespace-nowrap">
                            ~{{ number_format((float) $vacationPlan->estimated_distance_km, 1) }} km ({{ $vacationPlan->distance_source ?? 'Routing Engine' }})
                        </strong>
                    </div>
                @endif
                @if($vacationPlan->special_requirements)
                    <div class="pt-2 border-t border-slate-200/60">
                        <strong class="text-slate-800 block mb-0.5">Kebutuhan Khusus:</strong>
                        <p class="text-slate-600">{{ $vacationPlan->special_requirements }}</p>
                    </div>
                @endif
            </div>

            <!-- Route Map Section -->
            @php
                $hasOriginCoords = !is_null($vacationPlan->origin_latitude) && !is_null($vacationPlan->origin_longitude);
                $hasDestCoords = !is_null($vacationPlan->destination->latitude) && !is_null($vacationPlan->destination->longitude);
            @endphp

            @if($hasOriginCoords || $hasDestCoords)
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-700">Visualisasi Rute Perjalanan:</span>
                        <span class="text-[11px] text-slate-400">Hijau: Penjemputan • Biru: Destinasi Wisata</span>
                    </div>
                    <div id="plan-route-map" class="h-64 sm:h-80 w-full rounded-2xl border border-slate-300 shadow-inner"></div>
                </div>
            @endif
        </div>

        <div class="pt-6 flex justify-end">
            <a href="{{ route('customer.vacation-plans.recommendations', $vacationPlan) }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow transition">
                Lihat Armada Rekomendasi Terbaik →
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(!empty($hasOriginCoords) || !empty($hasDestCoords))
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const originLat = {{ $vacationPlan->origin_latitude ?? 'null' }};
    const originLng = {{ $vacationPlan->origin_longitude ?? 'null' }};
    const destLat = {{ $vacationPlan->destination->latitude ?? 'null' }};
    const destLng = {{ $vacationPlan->destination->longitude ?? 'null' }};

    const initialCenter = originLat ? [originLat, originLng] : [destLat, destLng];
    const map = L.map('plan-route-map').setView(initialCenter, 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const bounds = [];

    if (originLat && originLng) {
        L.marker([originLat, originLng]).addTo(map)
            .bindPopup('<strong>📍 Titik Penjemputan</strong><br>{{ addslashes($vacationPlan->origin_address) }}');
        bounds.push([originLat, originLng]);
    }

    if (destLat && destLng) {
        L.marker([destLat, destLng]).addTo(map)
            .bindPopup('<strong>🏖️ Destinasi: {{ addslashes($vacationPlan->destination->name) }}</strong>');
        bounds.push([destLat, destLng]);
    }

    if (originLat && originLng && destLat && destLng) {
        // Draw dashed line between origin and destination
        L.polyline([[originLat, originLng], [destLat, destLng]], {
            color: '#2563eb',
            weight: 3,
            dashArray: '6, 8',
            opacity: 0.8
        }).addTo(map);
    }

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }
});
</script>
@endif
@endpush
