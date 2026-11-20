@extends('layouts.app')

@section('title', 'Detail Rencana Liburan — TripMate')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('customer.vacation-plans.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali ke Daftar Rencana
        </a>
        <a href="{{ route('customer.vacation-plans.recommendations', $vacationPlan) }}" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold shadow hover:bg-blue-700 transition">
            Lihat Rekomendasi Mobil ({{ $vacationPlan->recommendations->count() }})
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs text-blue-600 font-semibold uppercase tracking-wider block">Destinasi</span>
                <h1 class="text-2xl font-bold text-slate-900 mt-0.5">{{ $vacationPlan->destination->name }}</h1>
                <span class="text-xs text-slate-500">📍 {{ $vacationPlan->destination->city }}, {{ $vacationPlan->destination->province }}</span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                {{ $vacationPlan->status->label() }}
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 border-b border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block">Mulai Sewa:</span>
                <strong class="text-slate-800">{{ $vacationPlan->start_date->format('d M Y') }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block">Selesai Sewa:</span>
                <strong class="text-slate-800">{{ $vacationPlan->end_date->format('d M Y') }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block">Jumlah Penumpang:</span>
                <strong class="text-slate-800">{{ $vacationPlan->passenger_count }} Orang</strong>
            </div>
            <div>
                <span class="text-slate-400 block">Kebutuhan Sopir:</span>
                <strong class="text-slate-800">{{ $vacationPlan->needs_driver ? 'Ya (+ Sopir)' : 'Lepas Kunci' }}</strong>
            </div>
        </div>

        <div class="py-6 space-y-3 text-xs text-slate-600">
            <div>
                <strong class="text-slate-800 block mb-0.5">Alamat Penjemputan:</strong>
                <p>{{ $vacationPlan->origin_address }}</p>
            </div>
            @if($vacationPlan->estimated_distance_km)
                <div>
                    <strong class="text-slate-800 block mb-0.5">Estimasi Jarak & Rute:</strong>
                    <p>{{ number_format((float) $vacationPlan->estimated_distance_km, 1) }} km ({{ $vacationPlan->distance_source ?? 'Routing API' }})</p>
                </div>
            @endif
            @if($vacationPlan->special_requirements)
                <div>
                    <strong class="text-slate-800 block mb-0.5">Kebutuhan Khusus:</strong>
                    <p>{{ $vacationPlan->special_requirements }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
