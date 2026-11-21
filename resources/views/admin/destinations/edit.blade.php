@extends('layouts.admin')

@section('title', 'Edit Destinasi — Admin TripMate')
@section('page_title', 'Edit Destinasi: ' . $destination->name)

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
<div class="max-w-4xl bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm">
    <form action="{{ route('admin.destinations.update', $destination) }}" method="POST" class="space-y-5 text-xs">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-bold text-slate-700 mb-1">Nama Destinasi <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $destination->name) }}" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block font-bold text-slate-700 mb-1">Slug URL</label>
            <input type="text" name="slug" value="{{ old('slug', $destination->slug) }}" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kota / Kabupaten <span class="text-rose-500">*</span></label>
                <input type="text" name="city" value="{{ old('city', $destination->city) }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Provinsi</label>
                <input type="text" name="province" value="{{ old('province', $destination->province) }}"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Interactive Map for Destination Coordinate Pinning -->
        <div class="pt-2">
            <div class="flex items-center justify-between mb-2">
                <label class="block font-bold text-slate-700">Titik Koordinat Peta Destinasi</label>
                <span class="text-[11px] text-slate-400">Klik atau geser pin marker pada peta</span>
            </div>
            <div class="relative mb-3">
                <div id="dest-picker-map" class="h-64 w-full rounded-xl border border-slate-300 shadow-inner"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Latitude</label>
                    <input type="number" step="any" name="latitude" id="dest_latitude" value="{{ old('latitude', $destination->latitude) }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Longitude</label>
                    <input type="number" step="any" name="longitude" id="dest_longitude" value="{{ old('longitude', $destination->longitude) }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Tipe Karakteristik Medan <span class="text-rose-500">*</span></label>
                <select name="terrain_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Enums\TerrainType::cases() as $t)
                        <option value="{{ $t->value }}" {{ old('terrain_type', $destination->terrain_type->value) === $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kondisi Jalan <span class="text-rose-500">*</span></label>
                <select name="road_condition" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Enums\RoadCondition::cases() as $r)
                        <option value="{{ $r->value }}" {{ old('road_condition', $destination->road_condition->value) === $r->value ? 'selected' : '' }}>{{ $r->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 mb-1">Deskripsi Singkat</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $destination->description) }}</textarea>
        </div>

        <div>
            <label class="block font-bold text-slate-700 mb-1">Catatan Akses Jalan & Tips</label>
            <textarea name="accessibility_notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('accessibility_notes', $destination->accessibility_notes) }}</textarea>
        </div>

        <div class="pt-2">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $destination->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="font-bold text-slate-700">Aktifkan destinasi ini untuk dipilih customer</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('admin.destinations.index') }}" class="text-slate-500 hover:underline">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                Perbarui Destinasi
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const latInput = document.getElementById('dest_latitude');
    const lngInput = document.getElementById('dest_longitude');

    const initialLat = parseFloat(latInput.value) || -7.7520;
    const initialLng = parseFloat(lngInput.value) || 110.4915;

    const map = L.map('dest-picker-map').setView([initialLat, initialLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

    function setCoords(lat, lng) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
    }

    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        setCoords(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        setCoords(e.latlng.lat, e.latlng.lng);
    });

    latInput.addEventListener('input', function () {
        const lat = parseFloat(this.value);
        const lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng]);
        }
    });

    lngInput.addEventListener('input', function () {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(this.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng]);
        }
    });
});
</script>
@endpush
