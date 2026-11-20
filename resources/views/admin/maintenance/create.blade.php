@extends('layouts.admin')

@section('title', 'Jadwalkan Perawatan — Admin TripMate')
@section('page_title', 'Jadwalkan Perawatan Kendaraan Baru')

@section('content')
<div class="max-w-xl bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
    <form action="{{ route('admin.maintenance.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf

        <div>
            <label class="block font-bold text-slate-700 mb-1">Pilih Unit Kendaraan <span class="text-rose-500">*</span></label>
            <select name="vehicle_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Mobil</option>
                @foreach($vehicles as $veh)
                    <option value="{{ $veh->id }}" {{ (old('vehicle_id', $selectedVehicleId) == $veh->id) ? 'selected' : '' }}>
                        {{ $veh->brand }} {{ $veh->model }} ({{ $veh->vehicle_code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Mulai Perawatan <span class="text-rose-500">*</span></label>
                <input type="datetime-local" name="start_datetime" value="{{ old('start_datetime', now()->format('Y-m-d\TH:i')) }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Selesai Perawatan (Estimasi)</label>
                <input type="datetime-local" name="end_datetime" value="{{ old('end_datetime', now()->addDays(2)->format('Y-m-d\TH:i')) }}"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 mb-1">Uraian / Deskripsi Perawatan <span class="text-rose-500">*</span></label>
            <textarea name="description" rows="3" required placeholder="Contoh: Servis berkala 20.000 km, ganti oli mesin dan kampas rem"
                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block font-bold text-slate-700 mb-1">Status Perawatan <span class="text-rose-500">*</span></label>
            <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach(\App\Enums\MaintenanceStatus::cases() as $ms)
                    <option value="{{ $ms->value }}" {{ old('status') === $ms->value ? 'selected' : '' }}>{{ $ms->label() }}</option>
                @endforeach
            </select>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('admin.maintenance.index') }}" class="text-slate-500 hover:underline">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                Simpan Jadwal Perawatan
            </button>
        </div>
    </form>
</div>
@endsection
