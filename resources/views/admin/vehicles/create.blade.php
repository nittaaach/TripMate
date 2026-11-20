@extends('layouts.admin')

@section('title', 'Tambah Armada — Admin TripMate')
@section('page_title', 'Tambah Unit Armada Mobil Baru')

@section('content')
<div class="max-w-3xl bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
    <form action="{{ route('admin.vehicles.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kode Unit <span class="text-rose-500">*</span></label>
                <input type="text" name="vehicle_code" value="{{ old('vehicle_code', 'TM-' . rand(100, 999)) }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Merek (Brand) <span class="text-rose-500">*</span></label>
                <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Toyota / Honda / Mitsubishi" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Model / Varian <span class="text-rose-500">*</span></label>
                <input type="text" name="model" value="{{ old('model') }}" placeholder="Innova Zenix / Pajero Sport" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Tahun</label>
                <input type="number" name="year" value="{{ old('year', date('Y')) }}"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                <select name="category" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Enums\VehicleCategory::cases() as $cat)
                        <option value="{{ $cat->value }}" {{ old('category') === $cat->value ? 'selected' : '' }}>{{ $cat->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Transmisi <span class="text-rose-500">*</span></label>
                <select name="transmission" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Enums\TransmissionType::cases() as $tr)
                        <option value="{{ $tr->value }}" {{ old('transmission') === $tr->value ? 'selected' : '' }}>{{ $tr->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Bahan Bakar <span class="text-rose-500">*</span></label>
                <select name="fuel_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Enums\FuelType::cases() as $f)
                        <option value="{{ $f->value }}" {{ old('fuel_type') === $f->value ? 'selected' : '' }}>{{ $f->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kapasitas Kursi (Termasuk Sopir) <span class="text-rose-500">*</span></label>
                <input type="number" name="seats" value="{{ old('seats', 7) }}" min="1" max="30" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Volume Bagasi (Liter)</label>
                <input type="number" name="luggage_capacity_l" value="{{ old('luggage_capacity_l', 400) }}"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Ground Clearance (mm)</label>
                <input type="number" name="ground_clearance_mm" value="{{ old('ground_clearance_mm', 190) }}"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Tarif Sewa Harian (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" name="daily_rate" value="{{ old('daily_rate', 500000) }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Deposit Jaminan (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" name="deposit_amount" value="{{ old('deposit_amount', 1000000) }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Suitable Terrain (Multi checkbox) -->
        <div>
            <label class="block font-bold text-slate-700 mb-1">Karakteristik Medan yang Didukung <span class="text-rose-500">*</span></label>
            <div class="grid grid-cols-3 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                @foreach(\App\Enums\TerrainType::cases() as $ter)
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="suitable_terrain[]" value="{{ $ter->value }}"
                            {{ in_array($ter->value, old('suitable_terrain', ['city', 'highway'])) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>{{ $ter->label() }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Suitable Road Conditions (Multi checkbox) -->
        <div>
            <label class="block font-bold text-slate-700 mb-1">Kondisi Jalan yang Sesuai <span class="text-rose-500">*</span></label>
            <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                @foreach(\App\Enums\RoadCondition::cases() as $rc)
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="suitable_road_conditions[]" value="{{ $rc->value }}"
                            {{ in_array($rc->value, old('suitable_road_conditions', ['good', 'moderate'])) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>{{ $rc->label() }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 pt-2">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Operasional</label>
                <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Enums\VehicleStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ old('status') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center pt-5">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="font-bold text-slate-700">Tersedia untuk disewa</span>
                </label>
            </div>
            <div class="flex items-center pt-5">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="has_ac" value="1" {{ old('has_ac', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="font-bold text-slate-700">Fasilitas AC Dingin</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 mb-1">Deskripsi Tambahan</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('admin.vehicles.index') }}" class="text-slate-500 hover:underline">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                Simpan & Tambah Foto →
            </button>
        </div>
    </form>
</div>
@endsection
