@extends('layouts.admin')

@section('title', 'Edit Armada — Admin TripMate')
@section('page_title', 'Edit Data Armada: ' . $vehicle->brand . ' ' . $vehicle->model)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Vehicle Edit Form (2 Columns) -->
    <div class="lg:col-span-2 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Unit <span class="text-rose-500">*</span></label>
                    <input type="text" name="vehicle_code" value="{{ old('vehicle_code', $vehicle->vehicle_code) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Merek <span class="text-rose-500">*</span></label>
                    <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Model <span class="text-rose-500">*</span></label>
                    <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tahun</label>
                    <input type="number" name="year" value="{{ old('year', $vehicle->year) }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(\App\Enums\VehicleCategory::cases() as $cat)
                            <option value="{{ $cat->value }}" {{ old('category', $vehicle->category->value) === $cat->value ? 'selected' : '' }}>{{ $cat->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Transmisi <span class="text-rose-500">*</span></label>
                    <select name="transmission" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(\App\Enums\TransmissionType::cases() as $tr)
                            <option value="{{ $tr->value }}" {{ old('transmission', $vehicle->transmission->value) === $tr->value ? 'selected' : '' }}>{{ $tr->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bahan Bakar <span class="text-rose-500">*</span></label>
                    <select name="fuel_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(\App\Enums\FuelType::cases() as $f)
                            <option value="{{ $f->value }}" {{ old('fuel_type', $vehicle->fuel_type->value) === $f->value ? 'selected' : '' }}>{{ $f->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kursi (Termasuk Sopir) <span class="text-rose-500">*</span></label>
                    <input type="number" name="seats" value="{{ old('seats', $vehicle->seats) }}" min="1" max="30" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Volume Bagasi (Liter)</label>
                    <input type="number" name="luggage_capacity_l" value="{{ old('luggage_capacity_l', $vehicle->luggage_capacity_l) }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ground Clearance (mm)</label>
                    <input type="number" name="ground_clearance_mm" value="{{ old('ground_clearance_mm', $vehicle->ground_clearance_mm) }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tarif Sewa Harian (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="daily_rate" value="{{ old('daily_rate', $vehicle->daily_rate) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deposit Jaminan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="deposit_amount" value="{{ old('deposit_amount', $vehicle->deposit_amount) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Suitable Terrain -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Karakteristik Medan yang Didukung <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-3 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    @php $currentTerrains = (array) $vehicle->suitable_terrain; @endphp
                    @foreach(\App\Enums\TerrainType::cases() as $ter)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="suitable_terrain[]" value="{{ $ter->value }}"
                                {{ in_array($ter->value, old('suitable_terrain', $currentTerrains)) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>{{ $ter->label() }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Suitable Road Conditions -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kondisi Jalan yang Sesuai <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    @php $currentRoads = (array) $vehicle->suitable_road_conditions; @endphp
                    @foreach(\App\Enums\RoadCondition::cases() as $rc)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="suitable_road_conditions[]" value="{{ $rc->value }}"
                                {{ in_array($rc->value, old('suitable_road_conditions', $currentRoads)) ? 'checked' : '' }}
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
                            <option value="{{ $st->value }}" {{ old('status', $vehicle->status->value) === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available', $vehicle->is_available) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-slate-700">Tersedia untuk disewa</span>
                    </label>
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="has_ac" value="1" {{ old('has_ac', $vehicle->has_ac) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-slate-700">Fasilitas AC Dingin</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Deskripsi Tambahan</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $vehicle->description) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.vehicles.index') }}" class="text-slate-500 hover:underline">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                    Perbarui Data Unit
                </button>
            </div>
        </form>
    </div>

    <!-- Images Management (1 Column) -->
    <div class="space-y-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-xs">
            <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Foto Armada</h3>

            <!-- Upload Photo Form -->
            <form action="{{ route('admin.vehicles.images.store', $vehicle) }}" method="POST" enctype="multipart/form-data" class="space-y-3 mb-6 bg-slate-50 p-4 rounded-xl border border-slate-200">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Unggah Foto Baru</label>
                    <input type="file" name="image" required accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alt Text (Aksesibilitas)</label>
                    <input type="text" name="alt_text" placeholder="Foto tampak depan" class="w-full px-3 py-1.5 border border-slate-300 rounded text-xs">
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_primary" value="1" class="rounded border-slate-300 text-blue-600">
                        <span>Jadikan Foto Utama</span>
                    </label>
                </div>
                <button type="submit" class="w-full py-2 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition">
                    Unggah Foto
                </button>
            </form>

            <!-- Existing Photos List -->
            <div class="space-y-3">
                @forelse($vehicle->images as $img)
                    <div class="flex items-center justify-between p-2 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-14 h-10 object-cover rounded">
                            <div>
                                @if($img->is_primary)
                                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 font-bold rounded text-[10px]">Utama</span>
                                @endif
                                <span class="text-slate-500 text-[11px] block">{{ $img->alt_text ?? 'Foto Unit' }}</span>
                            </div>
                        </div>
                        <form action="{{ route('admin.vehicles.images.destroy', [$vehicle, $img]) }}" method="POST" onsubmit="return confirm('Hapus foto ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs p-1">
                                ✕
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-center py-6 text-slate-400">Belum ada foto yang diunggah untuk mobil ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
