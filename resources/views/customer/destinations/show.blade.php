@extends('layouts.app')

@section('title', $destination->name . ' — TripMate')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('customer.destinations.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali ke Katalog Destinasi
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm p-8 sm:p-10">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-6 mb-6">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-blue-600">📍 {{ $destination->city }}, {{ $destination->province }}</span>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $destination->name }}</h1>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-blue-50 text-blue-700 font-semibold text-sm rounded-full">
                    Medan: {{ $destination->terrain_type->label() }}
                </span>
                <span class="px-3 py-1 bg-amber-50 text-amber-800 font-semibold text-sm rounded-full">
                    Jalan: {{ $destination->road_condition->label() }}
                </span>
            </div>
        </div>

        <div class="prose max-w-none text-slate-700 text-sm leading-relaxed mb-8">
            <h3 class="text-base font-bold text-slate-900 mb-2">Tentang Destinasi</h3>
            <p>{{ $destination->description ?? 'Tidak ada deskripsi detail.' }}</p>

            @if($destination->address)
                <h3 class="text-base font-bold text-slate-900 mt-6 mb-2">Alamat Lengkap</h3>
                <p>{{ $destination->address }}</p>
            @endif

            @if($destination->accessibility_notes)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mt-6">
                    <h4 class="text-sm font-bold text-amber-900 mb-1">⚠️ Catatan Aksesibilitas Jalan</h4>
                    <p class="text-xs text-amber-800">{{ $destination->accessibility_notes }}</p>
                </div>
            @endif
        </div>

        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                Koordinat: {{ $destination->latitude ?? '—' }}, {{ $destination->longitude ?? '—' }}
            </div>
            <a href="{{ route('customer.vacation-plans.create', ['destination_id' => $destination->id]) }}"
                class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition text-center">
                ✨ Buat Rencana Liburan ke Sini
            </a>
        </div>
    </div>
</div>
@endsection
