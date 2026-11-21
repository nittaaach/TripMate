@extends('layouts.app')

@section('title', $vehicle->brand . ' ' . $vehicle->model . ' — TripMate')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('customer.vehicles.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali ke Katalog Mobil
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Vehicle Details (2 Columns) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Image Gallery -->
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm p-4">
                <div class="h-72 sm:h-96 bg-slate-100 rounded-2xl overflow-hidden relative flex items-center justify-center">
                    @if($vehicle->primaryImage)
                        <img src="{{ asset('storage/' . $vehicle->primaryImage->image_path) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-6xl text-slate-300">🚗</span>
                    @endif
                </div>

                @if($vehicle->images->count() > 1)
                    <div class="grid grid-cols-4 gap-3 mt-4">
                        @foreach($vehicle->images as $img)
                            <div class="h-20 bg-slate-100 rounded-xl overflow-hidden border border-slate-200">
                                <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Specs & Description -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-1">
                    {{ $vehicle->brand }} {{ $vehicle->model }}
                </h1>
                <p class="text-sm text-slate-500 mb-6">Kode Unit: {{ $vehicle->vehicle_code }} • Tahun {{ $vehicle->year ?? 'Terbaru' }}</p>

                <h3 class="text-base font-bold text-slate-900 mb-3">Spesifikasi Kendaraan</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm mb-6">
                    <div class="bg-slate-50 p-3 rounded-xl">
                        <span class="text-xs text-slate-400 block">Kategori</span>
                        <strong class="text-slate-800">{{ $vehicle->category->label() }}</strong>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl">
                        <span class="text-xs text-slate-400 block">Transmisi</span>
                        <strong class="text-slate-800">{{ $vehicle->transmission->label() }}</strong>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl">
                        <span class="text-xs text-slate-400 block">Bahan Bakar</span>
                        <strong class="text-slate-800">{{ $vehicle->fuel_type->label() }}</strong>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl">
                        <span class="text-xs text-slate-400 block">Kapasitas Kursi</span>
                        <strong class="text-slate-800">{{ $vehicle->seats }} Orang (Termasuk Sopir)</strong>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl">
                        <span class="text-xs text-slate-400 block">Bagasi</span>
                        <strong class="text-slate-800">{{ $vehicle->luggage_capacity_l ?? '—' }} Liter</strong>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl">
                        <span class="text-xs text-slate-400 block">Ground Clearance</span>
                        <strong class="text-slate-800">{{ $vehicle->ground_clearance_mm ? $vehicle->ground_clearance_mm . ' mm' : '—' }}</strong>
                    </div>
                </div>

                <h3 class="text-base font-bold text-slate-900 mb-2">Karakteristik & Kemampuan Medan</h3>
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach((array) $vehicle->suitable_terrain as $terrain)
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full">
                            Medan: {{ ucfirst($terrain) }}
                        </span>
                    @endforeach
                    @foreach((array) $vehicle->suitable_road_conditions as $road)
                        <span class="px-3 py-1 bg-amber-50 text-amber-800 text-xs font-semibold rounded-full">
                            Jalan: {{ ucfirst($road) }}
                        </span>
                    @endforeach
                </div>

                @if($vehicle->description)
                    <h3 class="text-base font-bold text-slate-900 mb-2">Deskripsi</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $vehicle->description }}</p>
                @endif
            </div>

            <!-- Customer Reviews -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900">Ulasan Pengguna</h3>
                    <span class="text-sm font-semibold text-amber-500">
                        ★ {{ number_format($vehicle->average_rating, 1) }} ({{ $vehicle->reviews->count() }} ulasan)
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse($vehicle->reviews as $rev)
                        <div class="border-b border-slate-100 pb-4 last:border-0">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <strong class="text-slate-800">{{ $rev->user->name ?? 'Customer' }}</strong>
                                <span class="text-amber-500 font-bold">★ {{ $rev->rating }}/5</span>
                            </div>
                            <p class="text-xs text-slate-600">{{ $rev->comment ?? 'Penyewaan berjalan lancar dan memuaskan.' }}</p>
                            <span class="text-[10px] text-slate-400 mt-1 block">{{ $rev->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada ulasan untuk kendaraan ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Booking Sidebar Card (1 Column) -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm sticky top-24">
                <span class="text-xs text-slate-400 block mb-1">Tarif Sewa Harian</span>
                <div class="text-3xl font-extrabold text-blue-600 mb-4 whitespace-nowrap">
                    Rp {{ number_format((float) $vehicle->daily_rate, 0, ',', '.') }}
                    <span class="text-xs font-normal text-slate-400">/ hari</span>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl text-xs space-y-2 mb-6">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500">Deposit Jaminan:</span>
                        <strong class="text-slate-800 whitespace-nowrap shrink-0 text-right">Rp {{ number_format((float) $vehicle->deposit_amount, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Sopir Tersedia:</span>
                        <strong class="text-slate-800">{{ $vehicle->requires_driver ? 'Wajib Sopir' : 'Lepas Kunci / Sopir' }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status Unit:</span>
                        <span class="px-2 py-0.5 rounded-full font-semibold {{ $vehicle->is_available ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $vehicle->is_available ? 'Tersedia' : 'Sedang Digunakan' }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('customer.bookings.create', ['vehicle_id' => $vehicle->id]) }}"
                    class="block w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-center shadow-md transition">
                    Pesan Mobil Ini Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
