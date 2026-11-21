@extends('layouts.app')

@section('title', 'Bantu Pilihkan Mobil — Wizard Rekomendasi TripMate')

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
    <div class="mb-8 text-center">
        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 font-semibold text-xs rounded-full uppercase tracking-wider mb-2">
            ✨ Rekomendasi Cerdas
        </span>
        <h1 class="text-3xl font-extrabold text-slate-900">Bantu Pilihkan Mobil Liburan</h1>
        <p class="text-sm text-slate-500 mt-2 max-w-xl mx-auto">
            Tentukan destinasi dan titik keberangkatan Anda. Algoritma kami akan menganalisis kecocokan medan jalan, jarak rute, kapasitas penumpang, dan anggaran harian Anda.
        </p>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 shadow-sm">
        <form action="{{ route('customer.vacation-plans.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 1. Destinasi Wisata Tujuan -->
            <div>
                <label for="destination_id" class="block text-sm font-bold text-slate-800 mb-1">
                    Destinasi Wisata Tujuan <span class="text-rose-500">*</span>
                </label>
                <select name="destination_id" id="destination_id" required
                    class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Pilih Destinasi Wisata Tujuan --</option>
                    @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}" {{ (old('destination_id', request('destination_id')) == $dest->id) ? 'selected' : '' }}>
                            {{ $dest->name }} ({{ $dest->city }}, {{ $dest->province }}) — Medan: {{ $dest->terrain_type->label() }}, Jalan: {{ $dest->road_condition->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Titik Keberangkatan & Maps Penjemputan -->
            <div class="pt-2">
                <div class="flex items-center justify-between mb-2">
                    <label for="origin_address" class="block text-sm font-bold text-slate-800">
                        Titik Awal / Alamat Penjemputan (Maps & Alamat) <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" id="btn-origin-gps" class="text-xs text-blue-600 font-semibold hover:underline flex items-center gap-1">
                        📍 Gunakan Lokasi Saat Ini
                    </button>
                </div>

                <!-- Leaflet Interactive Map -->
                <div class="mb-3">
                    <div class="relative">
                        <div id="origin-map" class="h-64 sm:h-72 w-full rounded-2xl border border-slate-300 shadow-inner"></div>
                        <div class="absolute bottom-2 left-2 right-2 sm:right-auto bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-slate-200 text-[11px] text-slate-600 z-[400] flex items-center justify-between gap-3 shadow-sm">
                            <span>Geser pin atau klik di peta untuk menentukan titik awal Anda</span>
                            <span class="font-mono text-slate-800 font-semibold whitespace-nowrap" id="origin-coords-display">
                                Lat: -7.7956, Lng: 110.3695
                            </span>
                        </div>
                    </div>
                    <input type="hidden" name="origin_latitude" id="origin_latitude" value="{{ old('origin_latitude', '-7.7956000') }}">
                    <input type="hidden" name="origin_longitude" id="origin_longitude" value="{{ old('origin_longitude', '110.3695000') }}">
                </div>

                <textarea name="origin_address" id="origin_address" rows="2" required placeholder="Contoh: Hotel Tentrem, Jl. P. Mangkubumi No.72, Kota Yogyakarta"
                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('origin_address') }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Sistem akan menghitung estimasi jarak rute berkendara menuju destinasi secara otomatis.</p>
            </div>

            <!-- 3. Tanggal Sewa -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="start_date" class="block text-sm font-bold text-slate-800 mb-1">
                        Tanggal Mulai Sewa <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d', strtotime('+1 day'))) }}" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-bold text-slate-800 mb-1">
                        Tanggal Selesai Sewa <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+2 days'))) }}" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- 4. Penumpang, Bagasi & Tipe Trip (Responsive Grid) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
                <div>
                    <label for="passenger_count" class="block text-sm font-bold text-slate-800 mb-1">
                        Jumlah Penumpang <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="passenger_count" id="passenger_count" value="{{ old('passenger_count', 4) }}" min="1" max="25" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Hanya jumlah penumpang customer.</p>
                </div>

                <div>
                    <label for="luggage_level" class="block text-sm font-bold text-slate-800 mb-1">
                        Tingkat Bagasi <span class="text-rose-500">*</span>
                    </label>
                    <select name="luggage_level" id="luggage_level" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @foreach(\App\Enums\LuggageLevel::cases() as $luggage)
                            <option value="{{ $luggage->value }}" {{ old('luggage_level', 'medium') === $luggage->value ? 'selected' : '' }}>
                                {{ $luggage->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="trip_type" class="block text-sm font-bold text-slate-800 mb-1">
                        Tipe Perjalanan <span class="text-rose-500">*</span>
                    </label>
                    <select name="trip_type" id="trip_type" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @foreach(\App\Enums\TripType::cases() as $type)
                            <option value="{{ $type->value }}" {{ old('trip_type', 'round_trip') === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 5. Budget & Driver -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="budget_per_day" class="block text-sm font-bold text-slate-800 mb-1">
                        Batas Anggaran Harian (Opsional)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-sm font-bold text-slate-400">Rp</span>
                        <input type="number" name="budget_per_day" id="budget_per_day" value="{{ old('budget_per_day') }}" placeholder="Contoh: 600000"
                            class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center pt-2 sm:pt-6">
                    <label class="flex items-center space-x-3 cursor-pointer p-3.5 bg-slate-50 border border-slate-200 rounded-2xl w-full hover:bg-slate-100/70 transition">
                        <input type="checkbox" name="needs_driver" value="1" {{ old('needs_driver') ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-bold text-slate-800">Butuh Sopir (Driver)</span>
                            <span class="text-xs text-slate-500 block">Kapasitas penumpang akan disesuaikan (-1 kursi).</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 6. Kebutuhan Khusus -->
            <div class="pt-2">
                <label for="special_requirements" class="block text-sm font-bold text-slate-800 mb-1">
                    Kebutuhan Tambahan (Opsional)
                </label>
                <textarea name="special_requirements" id="special_requirements" rows="2" placeholder="Contoh: butuh mobil dengan ground clearance tinggi, atau ada lansia yang membutuhkan kursi rendah"
                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('special_requirements') }}</textarea>
            </div>

            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:underline">Batal</a>
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition">
                    ✨ Generate Rekomendasi Mobil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const initialLat = parseFloat(document.getElementById('origin_latitude').value) || -7.7956;
    const initialLng = parseFloat(document.getElementById('origin_longitude').value) || 110.3695;

    const originMap = L.map('origin-map').setView([initialLat, initialLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(originMap);

    const originMarker = L.marker([initialLat, initialLng], {
        draggable: true
    }).addTo(originMap);

    async function reverseGeocode(lat, lng) {
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
                headers: { 'Accept-Language': 'id' }
            });
            if (res.ok) {
                const data = await res.json();
                if (data && data.display_name) {
                    const txt = document.getElementById('origin_address');
                    if (!txt.value.trim()) {
                        txt.value = data.display_name;
                    }
                }
            }
        } catch (e) {}
    }

    function updateCoords(lat, lng, geocode = false) {
        document.getElementById('origin_latitude').value = lat.toFixed(7);
        document.getElementById('origin_longitude').value = lng.toFixed(7);
        document.getElementById('origin-coords-display').innerText = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`;
        if (geocode) {
            reverseGeocode(lat, lng);
        }
    }

    originMarker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        updateCoords(pos.lat, pos.lng, true);
    });

    originMap.on('click', function (e) {
        originMarker.setLatLng(e.latlng);
        updateCoords(e.latlng.lat, e.latlng.lng, true);
    });

    document.getElementById('btn-origin-gps').addEventListener('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                originMap.setView([lat, lng], 16);
                originMarker.setLatLng([lat, lng]);
                updateCoords(lat, lng, true);
            }, function () {
                alert('Tidak dapat mendeteksi lokasi GPS Anda. Pastikan izin lokasi browser aktif.');
            });
        }
    });
});
</script>
@endpush
