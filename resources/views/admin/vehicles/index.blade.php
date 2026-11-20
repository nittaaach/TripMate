@extends('layouts.admin')

@section('title', 'Kelola Armada Kendaraan — Admin TripMate')
@section('page_title', 'Master Data: Armada Mobil')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-slate-500">Kelola spesifikasi, tarif harian, kapasitas, dan foto armada kendaraan</p>
    <a href="{{ route('admin.vehicles.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow transition">
        + Tambah Unit Mobil
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse text-xs">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                <th class="p-4">Unit Mobil</th>
                <th class="p-4">Kategori & Transmisi</th>
                <th class="p-4">Kapasitas</th>
                <th class="p-4">Tarif / Hari</th>
                <th class="p-4">Ketersediaan</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($vehicles as $v)
                <tr class="hover:bg-slate-50/50">
                    <td class="p-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-10 bg-slate-100 rounded overflow-hidden flex items-center justify-center">
                                @if($v->primaryImage)
                                    <img src="{{ asset('storage/' . $v->primaryImage->image_path) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-lg">🚗</span>
                                @endif
                            </div>
                            <div>
                                <strong class="text-sm text-slate-900 block">{{ $v->brand }} {{ $v->model }}</strong>
                                <span class="font-mono text-slate-400 text-[11px]">{{ $v->vehicle_code }} • Thn {{ $v->year ?? '—' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-slate-600">
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold rounded block w-max mb-1">{{ $v->category->label() }}</span>
                        <span>{{ $v->transmission->label() }} • {{ $v->fuel_type->label() }}</span>
                    </td>
                    <td class="p-4 text-slate-600">
                        👥 <strong>{{ $v->seats }} Kursi</strong><br>
                        🧳 {{ $v->luggage_capacity_l ?? '—' }} L
                    </td>
                    <td class="p-4">
                        <strong class="text-blue-600 font-bold block text-sm">Rp {{ number_format((float) $v->daily_rate, 0, ',', '.') }}</strong>
                        <span class="text-slate-400 text-[10px]">Dep: Rp {{ number_format((float) $v->deposit_amount, 0, ',', '.') }}</span>
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-0.5 rounded-full font-semibold {{ $v->is_available ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $v->is_available ? 'Tersedia' : 'Non-Tersedia' }}
                        </span>
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-0.5 rounded-full font-semibold {{ $v->status->value === 'active' ? 'bg-blue-100 text-blue-800' : ($v->status->value === 'maintenance' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">
                            {{ $v->status->label() }}
                        </span>
                    </td>
                    <td class="p-4 text-right space-x-2">
                        <a href="{{ route('admin.vehicles.edit', $v) }}" class="px-2.5 py-1.5 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 font-semibold">
                            Edit & Foto
                        </a>
                        <form action="{{ route('admin.vehicles.destroy', $v) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kendaraan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1.5 text-rose-600 hover:bg-rose-50 rounded font-semibold">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-slate-400">Belum ada unit armada mobil.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4 border-t border-slate-100">
        {{ $vehicles->links() }}
    </div>
</div>
@endsection
