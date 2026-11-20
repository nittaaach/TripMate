@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Mobil — TripMate')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header Summary -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white rounded-3xl p-6 sm:p-8 mb-8 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-wider text-blue-300 font-semibold block mb-1">Rencana Liburan Anda</span>
                <h1 class="text-2xl sm:text-3xl font-bold">{{ $vacationPlan->destination->name }}</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    📍 Dari: {{ $vacationPlan->origin_address }} • 📅 {{ $vacationPlan->start_date->format('d M Y') }} s/d {{ $vacationPlan->end_date->format('d M Y') }} ({{ $vacationPlan->rental_days }} hari)
                </p>
            </div>
            <form action="{{ route('customer.vacation-plans.recommendations.regenerate', $vacationPlan) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-semibold rounded-xl backdrop-blur transition">
                    🔄 Hitung Ulang Rekomendasi
                </button>
            </form>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-white/10 text-xs">
            <div>
                <span class="text-blue-300 block">Karakter Medan:</span>
                <strong class="text-white">{{ $vacationPlan->destination->terrain_type->label() }}</strong>
            </div>
            <div>
                <span class="text-blue-300 block">Kondisi Jalan:</span>
                <strong class="text-white">{{ $vacationPlan->destination->road_condition->label() }}</strong>
            </div>
            <div>
                <span class="text-blue-300 block">Estimasi Jarak Rute:</span>
                <strong class="text-white">{{ $vacationPlan->estimated_distance_km ? number_format((float) $vacationPlan->estimated_distance_km, 1) . ' km' : 'Belum dihitung' }}</strong>
            </div>
            <div>
                <span class="text-blue-300 block">Penumpang & Sopir:</span>
                <strong class="text-white">{{ $vacationPlan->passenger_count }} org {{ $vacationPlan->needs_driver ? '(+ Sopir)' : '(Lepas kunci)' }}</strong>
            </div>
        </div>
    </div>

    <!-- Recommendations List -->
    <div class="space-y-6">
        @forelse($recommendations as $rec)
            @php $v = $rec->vehicle; @endphp
            <div class="bg-white rounded-3xl border {{ $rec->rank_position === 1 ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-slate-200' }} p-6 sm:p-8 shadow-sm hover:shadow-md transition relative">
                @if($rec->rank_position === 1)
                    <div class="absolute -top-3.5 left-8 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-extrabold uppercase px-3 py-1 rounded-full shadow">
                        🏆 Peringkat #1 Paling Direkomendasikan
                    </div>
                @else
                    <div class="absolute -top-3 left-8 bg-slate-800 text-white text-xs font-bold px-3 py-0.5 rounded-full">
                        Peringkat #{{ $rec->rank_position }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center mt-2">
                    <!-- Image -->
                    <div class="md:col-span-4 h-48 bg-slate-100 rounded-2xl overflow-hidden relative flex items-center justify-center">
                        @if($v->primaryImage)
                            <img src="{{ asset('storage/' . $v->primaryImage->image_path) }}" alt="{{ $v->brand }} {{ $v->model }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-5xl text-slate-300">🚗</span>
                        @endif
                        <span class="absolute top-3 left-3 bg-white/90 backdrop-blur text-xs font-bold px-2 py-0.5 rounded-full text-slate-800">
                            {{ $v->category->label() }}
                        </span>
                    </div>

                    <!-- Details & Breakdown -->
                    <div class="md:col-span-8 flex flex-col justify-between space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <h3 class="text-2xl font-extrabold text-slate-900">{{ $v->brand }} {{ $v->model }}</h3>
                                <p class="text-xs text-slate-500">{{ $v->transmission->label() }} • {{ $v->fuel_type->label() }} • {{ $v->seats }} Kursi</p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-black text-blue-600">
                                    {{ number_format((float) $rec->match_score, 0) }}%
                                </div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Tingkat Kesesuaian</span>
                            </div>
                        </div>

                        <!-- Transparent Explanation (PRD §8.3) -->
                        <div class="bg-blue-50/70 border border-blue-100 rounded-2xl p-4 text-xs text-blue-900 leading-relaxed">
                            <span class="font-bold block mb-1">💡 Alasan Rekomendasi:</span>
                            {{ $rec->recommendation_reason }}
                        </div>

                        <!-- Score Components Breakdown -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px] text-slate-600 bg-slate-50 p-3 rounded-xl">
                            <div>
                                <span class="text-slate-400 block">Kecocokan Medan:</span>
                                <strong>{{ number_format((float) $rec->terrain_fit_score, 0) }} / 100</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Kapasitas:</span>
                                <strong>{{ number_format((float) $rec->capacity_fit_score, 0) }} / 100</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Jarak Rute:</span>
                                <strong>{{ number_format((float) $rec->distance_fit_score, 0) }} / 100</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Kesesuaian Budget:</span>
                                <strong>{{ number_format((float) $rec->budget_fit_score, 0) }} / 100</strong>
                            </div>
                        </div>

                        <!-- Pricing & CTA -->
                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <span class="text-xs text-slate-400 block">Tarif per hari</span>
                                <span class="text-xl font-extrabold text-slate-900">
                                    Rp {{ number_format((float) $v->daily_rate, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-slate-500">/ hari</span>
                                </span>
                            </div>

                            <div class="flex items-center space-x-3 w-full sm:w-auto">
                                <a href="{{ route('customer.vehicles.show', $v) }}" class="px-4 py-2.5 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition text-center">
                                    Detail Mobil
                                </a>
                                <form action="{{ route('customer.vacation-plans.recommendations.select', [$vacationPlan, $rec]) }}" method="POST" class="flex-grow sm:flex-grow-0">
                                    @csrf
                                    <button type="submit" class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow transition">
                                        Pilih & Lanjutkan Booking →
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State (PRD FR-10) -->
            <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                <span class="text-5xl block mb-3">🔍</span>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Tidak Ada Mobil yang Memenuhi Syarat</h3>
                <p class="text-sm text-slate-500 max-w-lg mx-auto mb-6">
                    Tidak ditemukan armada aktif yang sesuai dengan kriteria wajib Anda (misalnya kapasitas kurang, bentrok jadwal booking/maintenance, atau medan yang sangat ekstrem).
                </p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('customer.vacation-plans.create') }}" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition">
                        Ubah Kriteria Pencarian
                    </a>
                    <a href="{{ route('customer.vehicles.index') }}" class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-50 transition">
                        Pilih Mobil Manual dari Katalog
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
