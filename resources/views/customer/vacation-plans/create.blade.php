@extends('layouts.app')

@section('title', 'Bantu Pilihkan Mobil — Wizard Rekomendasi TripMate')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8 text-center">
        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 font-semibold text-xs rounded-full uppercase tracking-wider mb-2">
            ✨ Rekomendasi Cerdas
        </span>
        <h1 class="text-3xl font-extrabold text-slate-900">Bantu Pilihkan Mobil Liburan</h1>
        <p class="text-sm text-slate-500 mt-2">
            Masukkan rencana perjalanan Anda. Algoritma kami akan mencocokkan kapasitas, rute, medan jalan, dan anggaran untuk Anda.
        </p>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-10 shadow-sm">
        <form action="{{ route('customer.vacation-plans.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 1. Destinasi -->
            <div>
                <label for="destination_id" class="block text-sm font-bold text-slate-800 mb-1">
                    Destinasi Wisata Tujuan <span class="text-rose-500">*</span>
                </label>
                <select name="destination_id" id="destination_id" required
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Pilih Destinasi Wisata</option>
                    @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}" {{ (old('destination_id', request('destination_id')) == $dest->id) ? 'selected' : '' }}>
                            {{ $dest->name }} ({{ $dest->city }}, {{ $dest->province }}) — Medan: {{ $dest->terrain_type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Titik Penjemputan -->
            <div>
                <label for="origin_address" class="block text-sm font-bold text-slate-800 mb-1">
                    Titik Awal / Alamat Penjemputan <span class="text-rose-500">*</span>
                </label>
                <textarea name="origin_address" id="origin_address" rows="2" required placeholder="Contoh: Hotel Tentrem, Jl. P. Mangkubumi No.72, Yogyakarta"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('origin_address') }}</textarea>
                <p class="text-xs text-slate-400 mt-1">Sistem akan menghitung jarak rute berkendara menuju destinasi secara otomatis.</p>
            </div>

            <!-- 3. Tanggal Sewa -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-bold text-slate-800 mb-1">
                        Tanggal Mulai Sewa <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d', strtotime('+1 day'))) }}" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-bold text-slate-800 mb-1">
                        Tanggal Selesai Sewa <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+2 days'))) }}" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- 4. Penumpang & Bagasi -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="passenger_count" class="block text-sm font-bold text-slate-800 mb-1">
                        Jumlah Penumpang <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="passenger_count" id="passenger_count" value="{{ old('passenger_count', 4) }}" min="1" max="25" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Hanya jumlah penumpang customer.</p>
                </div>

                <div>
                    <label for="luggage_level" class="block text-sm font-bold text-slate-800 mb-1">
                        Tingkat Bagasi <span class="text-rose-500">*</span>
                    </label>
                    <select name="luggage_level" id="luggage_level" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @foreach(\App\Enums\LuggageLevel::cases() as $luggage)
                            <option value="{{ $luggage->value }}" {{ old('luggage_level') === $luggage->value ? 'selected' : '' }}>
                                {{ $luggage->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="trip_type" class="block text-sm font-bold text-slate-800 mb-1">
                        Tipe Perjalanan <span class="text-rose-500">*</span>
                    </label>
                    <select name="trip_type" id="trip_type" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @foreach(\App\Enums\TripType::cases() as $type)
                            <option value="{{ $type->value }}" {{ old('trip_type') === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 5. Budget & Driver -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="budget_per_day" class="block text-sm font-bold text-slate-800 mb-1">
                        Batas Anggaran Harian (Opsional)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-sm text-slate-400">Rp</span>
                        <input type="number" name="budget_per_day" id="budget_per_day" value="{{ old('budget_per_day') }}" placeholder="Contoh: 600000"
                            class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="needs_driver" value="1" {{ old('needs_driver') ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-bold text-slate-800">Butuh Sopir (Driver)</span>
                            <span class="text-xs text-slate-400 block">Kapasitas kursi penumpang akan disesuaikan (-1 kursi).</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 6. Kebutuhan Khusus -->
            <div>
                <label for="special_requirements" class="block text-sm font-bold text-slate-800 mb-1">
                    Kebutuhan Tambahan (Opsional)
                </label>
                <textarea name="special_requirements" id="special_requirements" rows="2" placeholder="Contoh: butuh mobil tinggi untuk tanjakan curam, atau kursi bayi"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('special_requirements') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:underline">Batal</a>
                <button type="submit" class="px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition">
                    ✨ Generate Rekomendasi Mobil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
