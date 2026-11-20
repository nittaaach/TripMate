@extends('layouts.app')

@section('title', 'Katalog Destinasi Wisata — TripMate')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900">Destinasi Wisata</h1>
    <p class="text-sm text-slate-500 mt-1">Jelajahi berbagai lokasi wisata dan medan jalan sebelum memilih armada</p>
</div>

<!-- Search & Filter Bar -->
<form action="{{ route('customer.destinations.index') }}" method="GET" class="bg-white p-4 rounded-2xl border border-slate-200 mb-8 flex flex-col sm:flex-row gap-4 items-center">
    <div class="flex-grow w-full">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tempat, kota, atau provinsi..."
            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
    </div>
    <div class="w-full sm:w-48">
        <select name="terrain_type" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">Semua Medan</option>
            @foreach(\App\Enums\TerrainType::cases() as $terrain)
                <option value="{{ $terrain->value }}" {{ request('terrain_type') === $terrain->value ? 'selected' : '' }}>
                    {{ $terrain->label() }}
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm hover:bg-blue-700 transition">
        Filter
    </button>
</form>

<!-- Destination Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($destinations as $dest)
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div class="p-6">
                <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                    <span>📍 {{ $dest->city }}, {{ $dest->province }}</span>
                    <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 font-semibold rounded-full">{{ $dest->terrain_type->label() }}</span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">
                    <a href="{{ route('customer.destinations.show', $dest->slug) }}" class="hover:text-blue-600 transition">
                        {{ $dest->name }}
                    </a>
                </h3>
                <p class="text-sm text-slate-600 line-clamp-3 mb-4">{{ $dest->description ?? 'Destinasi wisata favorit.' }}</p>

                <div class="space-y-1.5 text-xs text-slate-500 border-t border-slate-100 pt-3">
                    <div>Kondisi Jalan: <strong class="text-slate-800">{{ $dest->road_condition->label() }}</strong></div>
                    @if($dest->accessibility_notes)
                        <div>Catatan Akses: <span class="italic text-slate-600">{{ $dest->accessibility_notes }}</span></div>
                    @endif
                </div>
            </div>

            <div class="p-6 pt-0 flex gap-2">
                <a href="{{ route('customer.destinations.show', $dest->slug) }}" class="flex-1 text-center py-2 text-xs font-semibold border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition">
                    Lihat Info
                </a>
                <a href="{{ route('customer.vacation-plans.create', ['destination_id' => $dest->id]) }}" class="flex-1 text-center py-2 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Pilih Destinasi
                </a>
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-slate-200">
            <p class="text-slate-500 text-sm">Tidak ada destinasi yang cocok dengan pencarian Anda.</p>
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $destinations->links() }}
</div>
@endsection
