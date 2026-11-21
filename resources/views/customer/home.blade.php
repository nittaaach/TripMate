@extends('layouts.app')

@section('title', 'TripMate — Rental Mobil Berbasis Preferensi Liburan')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-900 via-indigo-900 to-slate-900 text-white rounded-3xl p-8 sm:p-14 mb-12 shadow-xl relative overflow-hidden">
    <div class="max-w-2xl relative z-10">
        <span class="inline-block px-3 py-1 bg-blue-500/20 text-blue-300 border border-blue-400/30 rounded-full text-xs font-semibold mb-4 tracking-wide uppercase">
            Platform Sewa Mobil Cerdas
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
            Temukan Mobil Ideal untuk Rencana Liburan Anda.
        </h1>
        <p class="mt-4 text-base sm:text-lg text-slate-300 leading-relaxed">
            Tidak yakin mobil apa yang cocok untuk medan wisata Anda? Biarkan sistem cerdas kami menyaring kapasitas, jalan pegunungan, pantai, hingga efisiensi rute terbaik.
        </p>

        <!-- 2 Alur Pengguna (PRD Alur A & Alur B) -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4">
            <a href="{{ route('customer.vacation-plans.create') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                ✨ Bantu Pilihkan Mobil
            </a>
            <a href="{{ route('customer.vehicles.index') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-white/10 hover:bg-white/20 text-white border border-white/20 font-semibold rounded-xl backdrop-blur transition">
                🚗 Pilih Mobil Sendiri
            </a>
        </div>
    </div>
</section>

<!-- Populer Destinasi -->
<section class="mb-12">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Destinasi Wisata Pilihan</h2>
            <p class="text-sm text-slate-500">Pilih tempat liburan impian dan lihat kesesuaian medan perjalanannya</p>
        </div>
        <a href="{{ route('customer.destinations.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">Lihat Semua →</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($destinations as $dest)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="p-6">
                    <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                        <span>📍 {{ $dest->city }}, {{ $dest->province }}</span>
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-medium rounded-full">{{ $dest->terrain_type->label() }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $dest->name }}</h3>
                    <p class="text-sm text-slate-600 line-clamp-2 mb-4">{{ $dest->description ?? 'Destinasi wisata menarik yang siap dieksplorasi bersama TripMate.' }}</p>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-500">Jalan: <strong class="text-slate-700">{{ $dest->road_condition->label() }}</strong></span>
                        <a href="{{ route('customer.vacation-plans.create', ['destination_id' => $dest->id]) }}" class="text-xs font-semibold text-blue-600 hover:underline">Rencanakan →</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-3 text-center py-8 text-slate-400 text-sm">Belum ada destinasi aktif.</p>
        @endforelse
    </div>
</section>

<!-- Featured Vehicles -->
<section>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Armada Pilihan Kami</h2>
            <p class="text-sm text-slate-500">Unit terawat, prima, dan siap menemani perjalanan Anda</p>
        </div>
        <a href="{{ route('customer.vehicles.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">Lihat Semua Mobil →</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($featuredVehicles as $vehicle)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col">
                <div class="h-44 bg-slate-100 relative flex items-center justify-center overflow-hidden">
                    @if($vehicle->primaryImage)
                        <img src="{{ asset('storage/' . $vehicle->primaryImage->image_path) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl text-slate-300">🚗</span>
                    @endif
                    <span class="absolute top-3 right-3 bg-white/90 backdrop-blur text-xs font-bold px-2.5 py-1 rounded-full text-slate-800 shadow-sm">
                        {{ $vehicle->category->label() }}
                    </span>
                </div>
                <div class="p-5 flex-grow flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tahun {{ $vehicle->year ?? 'Terbaru' }} • {{ $vehicle->transmission->label() }}</p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-600">
                            <span class="bg-slate-100 px-2 py-1 rounded">👥 {{ $vehicle->seats }} Kursi</span>
                            <span class="bg-slate-100 px-2 py-1 rounded">⛽ {{ $vehicle->fuel_type->label() }}</span>
                            @if($vehicle->has_ac)
                                <span class="bg-slate-100 px-2 py-1 rounded">❄️ AC</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-xs text-slate-400 block">Tarif per hari</span>
                            <span class="text-base font-bold text-blue-600 whitespace-nowrap">Rp {{ number_format((float) $vehicle->daily_rate, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('customer.vehicles.show', $vehicle) }}" class="text-xs font-semibold bg-slate-900 text-white px-3.5 py-2 rounded-lg hover:bg-slate-800 transition shrink-0 whitespace-nowrap">
                            Detail Mobil
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-3 text-center py-8 text-slate-400 text-sm">Belum ada armada aktif.</p>
        @endforelse
    </div>
</section>
@endsection
