@extends('layouts.app')

@section('title', 'Form Pemesanan Rental — TripMate')

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
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('customer.vehicles.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali ke Katalog Armada
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Booking Form (Left Column, col-span-7 / 8) -->
        <div class="lg:col-span-7 xl:col-span-8">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="mb-6 pb-4 border-b border-slate-100">
                    <h1 class="text-2xl font-extrabold text-slate-900">Formulir Pemesanan</h1>
                    <p class="text-xs text-slate-500 mt-1">Lengkapi data jadwal, titik maps, dan rincian alamat serah terima kendaraan</p>
                </div>

                <form id="booking-form" action="{{ route('customer.bookings.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                    @if($plan)
                        <input type="hidden" name="vacation_plan_id" value="{{ $plan->id }}">
                    @endif

                    <!-- Section: Jadwal Sewa -->
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-black">1</span>
                            Jadwal Sewa Kendaraan
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div>
                                <label for="pickup_datetime" class="block text-xs font-bold text-slate-700 mb-1">
                                    Waktu Penjemputan / Ambil <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" name="pickup_datetime" id="pickup_datetime"
                                    value="{{ old('pickup_datetime', $pickupDatetime->format('Y-m-d\TH:i')) }}" required
                                    class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label for="return_datetime" class="block text-xs font-bold text-slate-700 mb-1">
                                    Waktu Pengembalian <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" name="return_datetime" id="return_datetime"
                                    value="{{ old('return_datetime', $returnDatetime->format('Y-m-d\TH:i')) }}" required
                                    class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Lokasi Penjemputan (Maps & Detail Indonesia) -->
                    <div class="pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-black">2</span>
                                Lokasi Penjemputan (Titik Maps & Alamat) <span class="text-rose-500">*</span>
                            </h2>
                            <button type="button" id="btn-pickup-gps" class="text-xs text-blue-600 font-semibold hover:underline flex items-center gap-1">
                                📍 Gunakan Lokasi Saat Ini
                            </button>
                        </div>

                        <!-- Map Container -->
                        <div class="mb-4">
                            <div class="relative">
                                <div id="pickup-map" class="h-64 sm:h-72 w-full rounded-2xl border border-slate-300 shadow-inner"></div>
                                <div class="absolute bottom-2 left-2 right-2 sm:right-auto bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-slate-200 text-[11px] text-slate-600 z-[400] flex items-center justify-between gap-3 shadow-sm">
                                    <span>Klik / geser pin untuk menentukan titik penjemputan</span>
                                    <span class="font-mono text-slate-800 font-semibold whitespace-nowrap" id="pickup-coords-display">
                                        Lat: -7.7956, Lng: 110.3695
                                    </span>
                                </div>
                            </div>
                            <!-- Hidden Coordinates -->
                            <input type="hidden" name="pickup_latitude" id="pickup_latitude" value="{{ old('pickup_latitude', '-7.7956000') }}">
                            <input type="hidden" name="pickup_longitude" id="pickup_longitude" value="{{ old('pickup_longitude', '110.3695000') }}">
                        </div>

                        <!-- Regional Cascade (Provinsi, Kota, Kecamatan, Kelurahan) -->
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-3.5">
                            <p class="text-xs font-semibold text-slate-600">Wilayah Administratif Indonesia:</p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label for="pickup_province_id" class="block text-xs font-medium text-slate-700 mb-1">Provinsi</label>
                                    <select name="pickup_province_id" id="pickup_province_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach($provinces as $prov)
                                            <option value="{{ $prov->id }}" {{ old('pickup_province_id', '34') == $prov->id ? 'selected' : '' }}>
                                                {{ $prov->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="pickup_regency_id" class="block text-xs font-medium text-slate-700 mb-1">Kota / Kabupaten</label>
                                    <select name="pickup_regency_id" id="pickup_regency_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option value="">-- Pilih Kota / Kabupaten --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label for="pickup_district_id" class="block text-xs font-medium text-slate-700 mb-1">Kecamatan</label>
                                    <select name="pickup_district_id" id="pickup_district_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="pickup_village_id" class="block text-xs font-medium text-slate-700 mb-1">Kelurahan / Desa</label>
                                    <select name="pickup_village_id" id="pickup_village_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option value="">-- Pilih Kelurahan / Desa --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label for="pickup_rt_rw" class="block text-xs font-medium text-slate-700 mb-1">RT / RW (Opsional)</label>
                                    <input type="text" name="pickup_rt_rw" id="pickup_rt_rw" value="{{ old('pickup_rt_rw') }}" placeholder="Contoh: RT 03 / RW 05"
                                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>

                                <div>
                                    <label for="pickup_postal_code" class="block text-xs font-medium text-slate-700 mb-1">Kode Pos</label>
                                    <input type="text" name="pickup_postal_code" id="pickup_postal_code" value="{{ old('pickup_postal_code') }}" placeholder="Contoh: 55271"
                                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>

                            <div>
                                <label for="pickup_address" class="block text-xs font-bold text-slate-700 mb-1">
                                    Detail Alamat Jalan, Gedung / Patokan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="pickup_address" id="pickup_address" rows="2" required placeholder="Contoh: Jl. Malioboro No. 45, depan lobby barat Hotel Grand Inna"
                                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('pickup_address', $pickupAddress) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Lokasi Pengembalian (Return) -->
                    <div class="pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-black">3</span>
                                Lokasi Pengembalian Mobil <span class="text-rose-500">*</span>
                            </h2>
                        </div>

                        <!-- Checkbox Lokasi Sama -->
                        <div class="mb-4 p-3.5 bg-blue-50/70 border border-blue-200 rounded-2xl flex items-center justify-between">
                            <label class="flex items-center space-x-2.5 cursor-pointer text-xs font-semibold text-blue-900 select-none">
                                <input type="checkbox" id="same_as_pickup" name="same_as_pickup" value="1" checked
                                    class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <span>Lokasi pengembalian sama dengan titik penjemputan</span>
                            </label>
                            <span class="text-[11px] text-blue-600 font-medium hidden sm:inline">Otomatis sinkron</span>
                        </div>

                        <!-- Return Address Container (Toggled via checkbox) -->
                        <div id="return-address-container" class="space-y-4 hidden">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Pilih titik maps pengembalian berbeda:</span>
                                <button type="button" id="btn-return-gps" class="text-xs text-blue-600 font-semibold hover:underline flex items-center gap-1">
                                    📍 Gunakan Lokasi Saat Ini
                                </button>
                            </div>

                            <!-- Map Container for Return -->
                            <div>
                                <div class="relative">
                                    <div id="return-map" class="h-64 sm:h-72 w-full rounded-2xl border border-slate-300 shadow-inner"></div>
                                    <div class="absolute bottom-2 left-2 right-2 sm:right-auto bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-slate-200 text-[11px] text-slate-600 z-[400] flex items-center justify-between gap-3 shadow-sm">
                                        <span>Titik pengembalian armada</span>
                                        <span class="font-mono text-slate-800 font-semibold whitespace-nowrap" id="return-coords-display">
                                            Lat: -7.7956, Lng: 110.3695
                                        </span>
                                    </div>
                                </div>
                                <input type="hidden" name="return_latitude" id="return_latitude" value="{{ old('return_latitude', '-7.7956000') }}">
                                <input type="hidden" name="return_longitude" id="return_longitude" value="{{ old('return_longitude', '110.3695000') }}">
                            </div>

                            <!-- Regional Cascade for Return -->
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-3.5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label for="return_province_id" class="block text-xs font-medium text-slate-700 mb-1">Provinsi</label>
                                        <select name="return_province_id" id="return_province_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                            <option value="">-- Pilih Provinsi --</option>
                                            @foreach($provinces as $prov)
                                                <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="return_regency_id" class="block text-xs font-medium text-slate-700 mb-1">Kota / Kabupaten</label>
                                        <select name="return_regency_id" id="return_regency_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                            <option value="">-- Pilih Kota / Kabupaten --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label for="return_district_id" class="block text-xs font-medium text-slate-700 mb-1">Kecamatan</label>
                                        <select name="return_district_id" id="return_district_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="return_village_id" class="block text-xs font-medium text-slate-700 mb-1">Kelurahan / Desa</label>
                                        <select name="return_village_id" id="return_village_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                            <option value="">-- Pilih Kelurahan / Desa --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label for="return_rt_rw" class="block text-xs font-medium text-slate-700 mb-1">RT / RW (Opsional)</label>
                                        <input type="text" name="return_rt_rw" id="return_rt_rw" value="{{ old('return_rt_rw') }}" placeholder="Contoh: RT 01 / RW 02"
                                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    </div>

                                    <div>
                                        <label for="return_postal_code" class="block text-xs font-medium text-slate-700 mb-1">Kode Pos</label>
                                        <input type="text" name="return_postal_code" id="return_postal_code" value="{{ old('return_postal_code') }}" placeholder="Contoh: 55271"
                                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                </div>

                                <div>
                                    <label for="return_address" class="block text-xs font-bold text-slate-700 mb-1">
                                        Detail Alamat Pengembalian <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea name="return_address" id="return_address" rows="2" placeholder="Alamat lengkap lokasi pengembalian mobil"
                                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('return_address', $returnAddress) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Layanan Sopir & Catatan -->
                    <div class="pt-4 border-t border-slate-100 space-y-4">
                        <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-slate-100/70 transition">
                            <input type="checkbox" name="needs_driver" value="1" {{ old('needs_driver', $needsDriver) ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-bold text-slate-800">Gunakan Layanan Sopir (+ Rp 150.000 / hari)</span>
                                <span class="text-xs text-slate-500 block">Sopir profesional berpengalaman siap mengantar Anda selama sewa.</span>
                            </div>
                        </label>

                        <div>
                            <label for="notes" class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" id="notes" rows="2" placeholder="Informasi khusus untuk staf serah terima"
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <span class="text-xs text-slate-400">Pemesanan aman dengan sistem anti double-booking.</span>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition">
                            Konfirmasi & Lanjut Pembayaran →
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Vehicle & Pricing Summary (Right Column, col-span-5 / 4) -->
        <div class="lg:col-span-5 xl:col-span-4 sticky top-24 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Ringkasan Armada</h3>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-16 h-12 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center">
                        @if($vehicle->primaryImage)
                            <img src="{{ asset('storage/' . $vehicle->primaryImage->image_path) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-xl">🚗</span>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <strong class="text-sm text-slate-900 block truncate">{{ $vehicle->brand }} {{ $vehicle->model }}</strong>
                        <span class="text-xs text-slate-500 block truncate">{{ $vehicle->category->label() }} • {{ $vehicle->seats }} Kursi</span>
                    </div>
                </div>

                <!-- Breakdown Pricing Box -->
                <div class="bg-slate-50 p-4 rounded-2xl text-xs space-y-2.5 mb-4 border border-slate-100">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500">Tarif Dasar / Hari:</span>
                        <strong class="text-slate-800 whitespace-nowrap shrink-0 text-right">
                            Rp {{ number_format($pricing['daily_rate_snapshot'], 0, ',', '.') }}
                        </strong>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500">Durasi Sewa:</span>
                        <strong class="text-slate-800 whitespace-nowrap shrink-0 text-right">
                            {{ $pricing['rental_days'] }} Hari
                        </strong>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500">Subtotal Sewa:</span>
                        <strong class="text-slate-800 whitespace-nowrap shrink-0 text-right">
                            Rp {{ number_format($pricing['subtotal'], 0, ',', '.') }}
                        </strong>
                    </div>
                    @if($pricing['additional_fee'] > 0)
                        <div class="flex items-center justify-between gap-2 text-blue-700">
                            <span>Biaya Sopir ({{ $pricing['rental_days'] }} hari):</span>
                            <strong class="whitespace-nowrap shrink-0 text-right">
                                + Rp {{ number_format($pricing['additional_fee'], 0, ',', '.') }}
                            </strong>
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-2 text-slate-500 pt-2 border-t border-slate-200">
                        <span>Deposit Jaminan (Kembali):</span>
                        <strong class="text-slate-700 whitespace-nowrap shrink-0 text-right">
                            Rp {{ number_format($pricing['deposit_amount'], 0, ',', '.') }}
                        </strong>
                    </div>
                </div>

                <!-- Total Row: Strictly Horizontal, whitespace-nowrap -->
                <div class="pt-3 border-t border-slate-100 flex items-baseline justify-between gap-3">
                    <span class="text-xs font-bold text-slate-800 whitespace-nowrap">Total Pembayaran:</span>
                    <span class="text-xl sm:text-2xl font-black text-blue-600 whitespace-nowrap shrink-0 text-right">
                        Rp {{ number_format($pricing['total_amount'], 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Information Tip -->
            <div class="bg-blue-50/60 border border-blue-200/80 rounded-2xl p-4 text-xs text-blue-900 space-y-1.5">
                <div class="font-bold flex items-center gap-1.5">
                    <span>💡</span> Info Serah Terima
                </div>
                <p class="text-slate-600 leading-relaxed">
                    Staf pengantaran kami akan menghubungi Anda sebelum waktu penjemputan dan menyesuaikan titik temu sesuai titik pin peta yang Anda pilih.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // Helper: Dynamic Cascading Dropdowns
    // -------------------------------------------------------------
    async function loadRegencies(provinceId, regencySelectId, selectedValue = '') {
        const select = document.getElementById(regencySelectId);
        select.innerHTML = '<option value="">-- Memuat Kota / Kabupaten... --</option>';
        if (!provinceId) {
            select.innerHTML = '<option value="">-- Pilih Kota / Kabupaten --</option>';
            return;
        }

        try {
            const res = await fetch(`/regional/regencies?province_id=${provinceId}`);
            const data = await res.json();
            let html = '<option value="">-- Pilih Kota / Kabupaten --</option>';
            data.forEach(item => {
                const sel = (item.id === selectedValue) ? 'selected' : '';
                html += `<option value="${item.id}" ${sel}>${item.name}</option>`;
            });
            select.innerHTML = html;
        } catch (e) {
            select.innerHTML = '<option value="">-- Gagal memuat data --</option>';
        }
    }

    async function loadDistricts(regencyId, districtSelectId, selectedValue = '') {
        const select = document.getElementById(districtSelectId);
        select.innerHTML = '<option value="">-- Memuat Kecamatan... --</option>';
        if (!regencyId) {
            select.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            return;
        }

        try {
            const res = await fetch(`/regional/districts?regency_id=${regencyId}`);
            const data = await res.json();
            let html = '<option value="">-- Pilih Kecamatan --</option>';
            data.forEach(item => {
                const sel = (item.id === selectedValue) ? 'selected' : '';
                html += `<option value="${item.id}" ${sel}>${item.name}</option>`;
            });
            select.innerHTML = html;
        } catch (e) {
            select.innerHTML = '<option value="">-- Gagal memuat data --</option>';
        }
    }

    async function loadVillages(districtId, villageSelectId, selectedValue = '') {
        const select = document.getElementById(villageSelectId);
        select.innerHTML = '<option value="">-- Memuat Kelurahan / Desa... --</option>';
        if (!districtId) {
            select.innerHTML = '<option value="">-- Pilih Kelurahan / Desa --</option>';
            return;
        }

        try {
            const res = await fetch(`/regional/villages?district_id=${districtId}`);
            const data = await res.json();
            let html = '<option value="">-- Pilih Kelurahan / Desa --</option>';
            data.forEach(item => {
                const sel = (item.id === selectedValue) ? 'selected' : '';
                html += `<option value="${item.id}" data-postal="${item.postal_code || ''}" ${sel}>${item.name}</option>`;
            });
            select.innerHTML = html;
        } catch (e) {
            select.innerHTML = '<option value="">-- Gagal memuat data --</option>';
        }
    }

    // Reverse geocode via Nominatim OSM
    async function reverseGeocode(lat, lng, targetTextareaId) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
                headers: { 'Accept-Language': 'id' }
            });
            if (response.ok) {
                const data = await response.json();
                if (data && data.display_name) {
                    const textarea = document.getElementById(targetTextareaId);
                    if (!textarea.value.trim()) {
                        textarea.value = data.display_name;
                    }
                }
            }
        } catch (e) {
            // Silently fail if offline or rate-limited
        }
    }

    // -------------------------------------------------------------
    // Pickup Map Initialization
    // -------------------------------------------------------------
    const initialPickupLat = parseFloat(document.getElementById('pickup_latitude').value) || -7.7956;
    const initialPickupLng = parseFloat(document.getElementById('pickup_longitude').value) || 110.3695;

    const pickupMap = L.map('pickup-map').setView([initialPickupLat, initialPickupLng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(pickupMap);

    const pickupMarker = L.marker([initialPickupLat, initialPickupLng], {
        draggable: true
    }).addTo(pickupMap);

    function updatePickupCoords(lat, lng, geocode = false) {
        document.getElementById('pickup_latitude').value = lat.toFixed(7);
        document.getElementById('pickup_longitude').value = lng.toFixed(7);
        document.getElementById('pickup_coords-display').innerText = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`;
        if (geocode) {
            reverseGeocode(lat, lng, 'pickup_address');
        }
    }

    pickupMarker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        updatePickupCoords(pos.lat, pos.lng, true);
    });

    pickupMap.on('click', function (e) {
        pickupMarker.setLatLng(e.latlng);
        updatePickupCoords(e.latlng.lat, e.latlng.lng, true);
    });

    document.getElementById('btn-pickup-gps').addEventListener('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                pickupMap.setView([lat, lng], 16);
                pickupMarker.setLatLng([lat, lng]);
                updatePickupCoords(lat, lng, true);
            }, function () {
                alert('Tidak dapat mendeteksi lokasi GPS Anda. Pastikan izin lokasi aktif.');
            });
        }
    });

    // Pickup Regional Cascades
    const pickupProv = document.getElementById('pickup_province_id');
    const pickupReg = document.getElementById('pickup_regency_id');
    const pickupDist = document.getElementById('pickup_district_id');
    const pickupVill = document.getElementById('pickup_village_id');
    const pickupPostal = document.getElementById('pickup_postal_code');

    pickupProv.addEventListener('change', function () {
        loadRegencies(this.value, 'pickup_regency_id');
        pickupDist.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        pickupVill.innerHTML = '<option value="">-- Pilih Kelurahan / Desa --</option>';
    });

    pickupReg.addEventListener('change', function () {
        loadDistricts(this.value, 'pickup_district_id');
        pickupVill.innerHTML = '<option value="">-- Pilih Kelurahan / Desa --</option>';
    });

    pickupDist.addEventListener('change', function () {
        loadVillages(this.value, 'pickup_village_id');
    });

    pickupVill.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.postal && !pickupPostal.value.trim()) {
            pickupPostal.value = opt.dataset.postal;
        }
    });

    // Auto load initial regencies for default province (DI Yogyakarta = 34)
    if (pickupProv.value) {
        loadRegencies(pickupProv.value, 'pickup_regency_id', '{{ old('pickup_regency_id', '3471') }}').then(() => {
            const initialReg = '{{ old('pickup_regency_id', '3471') }}';
            if (initialReg) {
                loadDistricts(initialReg, 'pickup_district_id', '{{ old('pickup_district_id', '347101') }}').then(() => {
                    const initialDist = '{{ old('pickup_district_id', '347101') }}';
                    if (initialDist) {
                        loadVillages(initialDist, 'pickup_village_id', '{{ old('pickup_village_id', '34710101') }}');
                    }
                });
            }
        });
    }

    // -------------------------------------------------------------
    // Return Map & Return Address Toggle
    // -------------------------------------------------------------
    let returnMap = null;
    let returnMarker = null;
    const sameCheckbox = document.getElementById('same_as_pickup');
    const returnContainer = document.getElementById('return-address-container');

    function initReturnMap() {
        if (returnMap) {
            setTimeout(() => returnMap.invalidateSize(), 200);
            return;
        }

        const initialReturnLat = parseFloat(document.getElementById('return_latitude').value) || initialPickupLat;
        const initialReturnLng = parseFloat(document.getElementById('return_longitude').value) || initialPickupLng;

        returnMap = L.map('return-map').setView([initialReturnLat, initialReturnLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(returnMap);

        returnMarker = L.marker([initialReturnLat, initialReturnLng], {
            draggable: true
        }).addTo(returnMap);

        function updateReturnCoords(lat, lng, geocode = false) {
            document.getElementById('return_latitude').value = lat.toFixed(7);
            document.getElementById('return_longitude').value = lng.toFixed(7);
            document.getElementById('return-coords-display').innerText = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`;
            if (geocode) {
                reverseGeocode(lat, lng, 'return_address');
            }
        }

        returnMarker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateReturnCoords(pos.lat, pos.lng, true);
        });

        returnMap.on('click', function (e) {
            returnMarker.setLatLng(e.latlng);
            updateReturnCoords(e.latlng.lat, e.latlng.lng, true);
        });

        document.getElementById('btn-return-gps').addEventListener('click', function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    returnMap.setView([lat, lng], 16);
                    returnMarker.setLatLng([lat, lng]);
                    updateReturnCoords(lat, lng, true);
                });
            }
        });

        // Return Regional Cascades
        const returnProv = document.getElementById('return_province_id');
        const returnReg = document.getElementById('return_regency_id');
        const returnDist = document.getElementById('return_district_id');
        const returnVill = document.getElementById('return_village_id');
        const returnPostal = document.getElementById('return_postal_code');

        returnProv.addEventListener('change', function () {
            loadRegencies(this.value, 'return_regency_id');
            returnDist.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            returnVill.innerHTML = '<option value="">-- Pilih Kelurahan / Desa --</option>';
        });

        returnReg.addEventListener('change', function () {
            loadDistricts(this.value, 'return_district_id');
            returnVill.innerHTML = '<option value="">-- Pilih Kelurahan / Desa --</option>';
        });

        returnDist.addEventListener('change', function () {
            loadVillages(this.value, 'return_village_id');
        });

        returnVill.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.postal && !returnPostal.value.trim()) {
                returnPostal.value = opt.dataset.postal;
            }
        });
    }

    sameCheckbox.addEventListener('change', function () {
        if (this.checked) {
            returnContainer.classList.add('hidden');
        } else {
            returnContainer.classList.remove('hidden');
            initReturnMap();
        }
    });

    // Form submit handler: if same_as_pickup is checked, clone pickup values to return values
    document.getElementById('booking-form').addEventListener('submit', function () {
        if (sameCheckbox.checked) {
            document.getElementById('return_address').value = document.getElementById('pickup_address').value;
            document.getElementById('return_latitude').value = document.getElementById('pickup_latitude').value;
            document.getElementById('return_longitude').value = document.getElementById('pickup_longitude').value;
            document.getElementById('return_province_id').value = document.getElementById('pickup_province_id').value;
            document.getElementById('return_regency_id').value = document.getElementById('pickup_regency_id').value;
            document.getElementById('return_district_id').value = document.getElementById('pickup_district_id').value;
            document.getElementById('return_village_id').value = document.getElementById('pickup_village_id').value;
            document.getElementById('return_rt_rw').value = document.getElementById('pickup_rt_rw').value;
            document.getElementById('return_postal_code').value = document.getElementById('pickup_postal_code').value;
        }
    });
});
</script>
@endpush
