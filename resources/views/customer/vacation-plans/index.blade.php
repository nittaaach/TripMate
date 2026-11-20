@extends('layouts.app')

@section('title', 'Rencana Liburan Saya — TripMate')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Rencana Liburan Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola rencana perjalanan dan hasil rekomendasi armada Anda</p>
    </div>
    <a href="{{ route('customer.vacation-plans.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition">
        + Buat Rencana Baru
    </a>
</div>

<div class="space-y-4">
    @forelse($plans as $plan)
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center space-x-3">
                    <h3 class="text-xl font-bold text-slate-900">{{ $plan->destination->name ?? 'Destinasi Wisata' }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $plan->status->value === 'recommended' ? 'bg-blue-100 text-blue-800' : ($plan->status->value === 'booked' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                        {{ $plan->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    📍 Dari: {{ Str::limit($plan->origin_address, 50) }}
                </p>
                <div class="flex flex-wrap gap-4 text-xs text-slate-600 pt-2">
                    <span>📅 {{ $plan->start_date->format('d M Y') }} - {{ $plan->end_date->format('d M Y') }} ({{ $plan->rental_days }} hari)</span>
                    <span>👥 {{ $plan->passenger_count }} Penumpang</span>
                    <span>🧳 Bagasi: {{ $plan->luggage_level->label() }}</span>
                    @if($plan->estimated_distance_km)
                        <span>🚗 Rute: ~{{ number_format((float) $plan->estimated_distance_km, 1) }} km</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4 sm:pt-0 border-t sm:border-0 border-slate-100">
                <a href="{{ route('customer.vacation-plans.recommendations', $plan) }}" class="px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-semibold transition">
                    Lihat Rekomendasi ({{ $plan->recommendations->count() }})
                </a>
                <form action="{{ route('customer.vacation-plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Hapus rencana liburan ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-semibold transition">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center py-12 bg-white rounded-2xl border border-slate-200">
            <span class="text-4xl text-slate-300 block mb-2">🏖️</span>
            <p class="text-slate-600 font-semibold mb-1">Belum ada rencana liburan yang dibuat.</p>
            <p class="text-xs text-slate-400 mb-6">Mulai dengan memasukkan tujuan wisata dan preferensi Anda.</p>
            <a href="{{ route('customer.vacation-plans.create') }}" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm hover:bg-blue-700 transition">
                ✨ Buat Rencana Liburan Sekarang
            </a>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $plans->links() }}
</div>
@endsection
