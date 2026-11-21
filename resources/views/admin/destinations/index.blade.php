@extends('layouts.admin')

@section('title', 'Kelola Destinasi Wisata — Admin TripMate')
@section('page_title', 'Master Data: Destinasi Wisata')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <p class="text-sm text-slate-500">Kelola master lokasi wisata, koordinat, dan kondisi jalan untuk mesin rekomendasi</p>
    <a href="{{ route('admin.destinations.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow transition self-start sm:self-auto whitespace-nowrap">
        + Tambah Destinasi
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs min-w-[700px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider whitespace-nowrap">
                    <th class="p-4">Nama Destinasi</th>
                    <th class="p-4">Kota / Provinsi</th>
                    <th class="p-4">Tipe Medan</th>
                    <th class="p-4">Kondisi Jalan</th>
                    <th class="p-4">Koordinat</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($destinations as $dest)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4 whitespace-nowrap">
                            <strong class="text-sm text-slate-900 block">{{ $dest->name }}</strong>
                            <span class="text-slate-400 font-mono text-[11px]">{{ $dest->slug }}</span>
                        </td>
                        <td class="p-4 text-slate-600 whitespace-nowrap">{{ $dest->city }}, {{ $dest->province ?? '—' }}</td>
                        <td class="p-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 font-semibold rounded-full">{{ $dest->terrain_type->label() }}</span>
                        </td>
                        <td class="p-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 bg-amber-50 text-amber-800 font-semibold rounded-full">{{ $dest->road_condition->label() }}</span>
                        </td>
                        <td class="p-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                            @if($dest->latitude && $dest->longitude)
                                {{ number_format($dest->latitude, 4) }}, {{ number_format($dest->longitude, 4) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="p-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full font-semibold {{ $dest->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $dest->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.destinations.edit', $dest) }}" class="px-2.5 py-1.5 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 font-semibold inline-block">
                                Edit
                            </a>
                            <form action="{{ route('admin.destinations.destroy', $dest) }}" method="POST" class="inline" onsubmit="return confirm('Hapus destinasi ini?');">
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
                        <td colspan="7" class="p-8 text-center text-slate-400">Belum ada data destinasi wisata.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100">
        {{ $destinations->links() }}
    </div>
</div>
@endsection
