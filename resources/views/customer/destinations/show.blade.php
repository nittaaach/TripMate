@extends('layouts.app')

@section('title', $destination->name . ' — TripMate')

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
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('customer.destinations.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali ke Katalog Destinasi
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm p-6 sm:p-10">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-6 mb-6">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-blue-600">📍 {{ $destination->city }}, {{ $destination->province }}</span>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $destination->name }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 bg-blue-50 text-blue-700 font-semibold text-sm rounded-full whitespace-nowrap">
                    Medan: {{ $destination->terrain_type->label() }}
                </span>
                <span class="px-3 py-1 bg-amber-50 text-amber-800 font-semibold text-sm rounded-full whitespace-nowrap">
                    Jalan: {{ $destination->road_condition->label() }}
                </span>
            </div>
        </div>

        <div class="prose max-w-none text-slate-700 text-sm leading-relaxed mb-6">
            <h3 class="text-base font-bold text-slate-900 mb-2">Tentang Destinasi</h3>
            <p>{{ $destination->description ?? 'Tidak ada deskripsi detail.' }}</p>

            @if($destination->address)
                <h3 class="text-base font-bold text-slate-900 mt-6 mb-2">Alamat Lengkap</h3>
                <p>{{ $destination->address }}</p>
            @endif

            @if($destination->accessibility_notes)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mt-6">
                    <h4 class="text-sm font-bold text-amber-900 mb-1">⚠️ Catatan Aksesibilitas Jalan</h4>
                    <p class="text-xs text-amber-800">{{ $destination->accessibility_notes }}</p>
                </div>
            @endif
        </div>

        <!-- Destination Map Section -->
        @if(!is_null($destination->latitude) && !is_null($destination->longitude))
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Lokasi Peta Destinasi:</h3>
                    <a href="https://www.google.com/maps?q={{ $destination->latitude }},{{ $destination->longitude }}" target="_blank" class="text-xs text-blue-600 font-semibold hover:underline flex items-center gap-1">
                        Buka Petunjuk Arah Google Maps ↗
                    </a>
                </div>
                <div id="destination-map" class="h-64 sm:h-72 w-full rounded-2xl border border-slate-300 shadow-inner"></div>
            </div>
        @endif

        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500 whitespace-nowrap">
                Koordinat: {{ $destination->latitude ?? '—' }}, {{ $destination->longitude ?? '—' }}
            </div>
            <a href="{{ route('customer.vacation-plans.create', ['destination_id' => $destination->id]) }}"
                class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition text-center whitespace-nowrap">
                ✨ Buat Rencana Liburan ke Sini
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(!is_null($destination->latitude) && !is_null($destination->longitude))
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lat = {{ $destination->latitude }};
    const lng = {{ $destination->longitude }};

    const map = L.map('destination-map').setView([lat, lng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup('<strong>🏖️ {{ addslashes($destination->name) }}</strong><br>{{ addslashes($destination->city) }}, {{ addslashes($destination->province) }}')
        .openPopup();
});
</script>
@endif
@endpush
