@extends('layouts.app')

@section('title', 'Katalog Armada Mobil — TripMate')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Katalog Armada Mobil</h1>
        <p class="text-sm text-slate-500 mt-1">Pilih langsung unit mobil yang Anda inginkan (Alur Mandiri)</p>
    </div>
    <a href="{{ route('customer.vacation-plans.create') }}" class="px-5 py-2.5 bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold rounded-xl text-sm transition">
        ✨ Belum tahu mobil apa? Gunakan Rekomendasi
    </a>
</div>

<!-- Filters -->
<form action="{{ route('customer.vehicles.index') }}" method="GET" class="bg-white p-5 rounded-2xl border border-slate-200 mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori</label>
        <select name="category" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Kategori</option>
            @foreach(\App\Enums\VehicleCategory::cases() as $cat)
                <option value="{{ $cat->value }}" {{ request('category') === $cat->value ? 'selected' : '' }}>{{ $cat->label() }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">Transmisi</label>
        <select name="transmission" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Transmisi</option>
            @foreach(\App\Enums\TransmissionType::cases() as $trans)
                <option value="{{ $trans->value }}" {{ request('transmission') === $trans->value ? 'selected' : '' }}>{{ $trans->label() }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">Min. Kursi</label>
        <select name="min_seats" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Berapapun</option>
            <option value="4" {{ request('min_seats') === '4' ? 'selected' : '' }}>Min 4 Kursi</option>
            <option value="6" {{ request('min_seats') === '6' ? 'selected' : '' }}>Min 6 Kursi</option>
            <option value="7" {{ request('min_seats') === '7' ? 'selected' : '' }}>Min 7 Kursi</option>
            <option value="10" {{ request('min_seats') === '10' ? 'selected' : '' }}>Min 10 Kursi (Minibus/Van)</option>
        </select>
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">Bahan Bakar</label>
        <select name="fuel_type" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua</option>
            @foreach(\App\Enums\FuelType::cases() as $fuel)
                <option value="{{ $fuel->value }}" {{ request('fuel_type') === $fuel->value ? 'selected' : '' }}>{{ $fuel->label() }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex items-end">
        <button type="submit" class="w-full py-2 bg-blue-600 text-white font-semibold rounded-lg text-sm hover:bg-blue-700 transition">
            Terapkan Filter
        </button>
    </div>
</form>

<!-- Vehicles Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($vehicles as $vehicle)
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div>
                <div class="h-48 bg-slate-100 relative flex items-center justify-center overflow-hidden">
                    @if($vehicle->primaryImage)
                        <img src="{{ asset('storage/' . $vehicle->primaryImage->image_path) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-5xl text-slate-300">🚗</span>
                    @endif
                    <span class="absolute top-3 right-3 bg-white/90 backdrop-blur text-xs font-bold px-2.5 py-1 rounded-full text-slate-800 shadow-sm">
                        {{ $vehicle->category->label() }}
                    </span>
                </div>

                <div class="p-6">
                    <div class="text-xs text-slate-400 font-semibold mb-1 uppercase tracking-wider">{{ $vehicle->brand }}</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">
                        <a href="{{ route('customer.vehicles.show', $vehicle) }}" class="hover:text-blue-600 transition">
                            {{ $vehicle->brand }} {{ $vehicle->model }}
                        </a>
                    </h3>

                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 my-4 bg-slate-50 p-3 rounded-xl">
                        <div>👥 <strong>{{ $vehicle->seats }} Kursi</strong></div>
                        <div>⚙️ <strong>{{ $vehicle->transmission->label() }}</strong></div>
                        <div>⛽ <strong>{{ $vehicle->fuel_type->label() }}</strong></div>
                        <div>🧳 <strong>{{ $vehicle->luggage_capacity_l ?? '—' }} Liter</strong></div>
                    </div>

                    @if($vehicle->description)
                        <p class="text-xs text-slate-500 line-clamp-2 mb-4">{{ $vehicle->description }}</p>
                    @endif
                </div>
            </div>

            <div class="p-6 pt-0 border-t border-slate-100 mt-2 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 block">Tarif Sewa</span>
                    <span class="text-lg font-bold text-blue-600">Rp {{ number_format((float) $vehicle->daily_rate, 0, ',', '.') }}<span class="text-xs text-slate-400 font-normal">/hari</span></span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('customer.vehicles.show', $vehicle) }}" class="px-3.5 py-2 border border-slate-300 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-50 transition">
                        Detail
                    </a>
                    <a href="{{ route('customer.bookings.create', ['vehicle_id' => $vehicle->id]) }}" class="px-3.5 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition">
                        Pesan
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-slate-200">
            <p class="text-slate-500 text-sm">Tidak ada kendaraan yang sesuai dengan kriteria filter.</p>
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $vehicles->links() }}
</div>
@endsection
