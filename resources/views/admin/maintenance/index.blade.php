@extends('layouts.admin')

@section('title', 'Jadwal Perawatan Armada — Admin TripMate')
@section('page_title', 'Jadwal Perawatan (Maintenance) Armada')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-slate-500">Unit dalam periode perawatan aktif otomatis diblokir dari reservasi sistem</p>
    <a href="{{ route('admin.maintenance.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow transition">
        + Jadwalkan Perawatan
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse text-xs">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                <th class="p-4">Kendaraan</th>
                <th class="p-4">Uraian / Jenis Perawatan</th>
                <th class="p-4">Mulai Perawatan</th>
                <th class="p-4">Selesai Perawatan</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($maintenances as $m)
                <tr class="hover:bg-slate-50/50">
                    <td class="p-4">
                        <strong class="text-sm text-slate-900 block">{{ $m->vehicle->brand }} {{ $m->vehicle->model }}</strong>
                        <span class="text-slate-400 font-mono text-[11px]">{{ $m->vehicle->vehicle_code }}</span>
                    </td>
                    <td class="p-4 text-slate-700">
                        {{ $m->description }}
                    </td>
                    <td class="p-4 text-slate-600">
                        {{ $m->start_datetime->format('d M Y, H:i') }}
                    </td>
                    <td class="p-4 text-slate-600">
                        {{ $m->end_datetime ? $m->end_datetime->format('d M Y, H:i') : 'Sampai selesai' }}
                    </td>
                    <td class="p-4">
                        <span class="px-2.5 py-0.5 rounded-full font-bold {{ $m->status->value === 'in_progress' ? 'bg-amber-100 text-amber-800' : ($m->status->value === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                            {{ $m->status->label() }}
                        </span>
                    </td>
                    <td class="p-4 text-right space-x-2">
                        <a href="{{ route('admin.maintenance.edit', $m) }}" class="px-2.5 py-1.5 border border-slate-300 rounded font-semibold text-slate-700 hover:bg-slate-100">
                            Edit
                        </a>
                        <form action="{{ route('admin.maintenance.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jadwal perawatan ini?');">
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
                    <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada jadwal perawatan armada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4 border-t border-slate-100">
        {{ $maintenances->links() }}
    </div>
</div>
@endsection
