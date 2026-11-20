@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran — Admin TripMate')
@section('page_title', 'Verifikasi Pembayaran Manual')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse text-xs">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                <th class="p-4">Kode Booking</th>
                <th class="p-4">Customer</th>
                <th class="p-4">Nominal</th>
                <th class="p-4">Metode</th>
                <th class="p-4">Waktu Bayar</th>
                <th class="p-4">Bukti Transfer</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Aksi Verifikasi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($payments as $p)
                <tr class="hover:bg-slate-50/50">
                    <td class="p-4 font-mono font-bold text-blue-600">
                        <a href="{{ route('admin.bookings.show', $p->booking) }}" class="hover:underline">
                            {{ $p->booking->booking_code }}
                        </a>
                    </td>
                    <td class="p-4">
                        <strong class="text-slate-800 block">{{ $p->booking->user->name }}</strong>
                        <span class="text-slate-400 text-[11px]">{{ $p->booking->vehicle->brand }} {{ $p->booking->vehicle->model }}</span>
                    </td>
                    <td class="p-4 font-bold text-slate-900 text-sm">
                        Rp {{ number_format((float) $p->amount, 0, ',', '.') }}
                    </td>
                    <td class="p-4 text-slate-600">{{ $p->payment_method->label() }}</td>
                    <td class="p-4 text-slate-500">
                        {{ $p->paid_at ? $p->paid_at->format('d M Y, H:i') : '—' }}
                    </td>
                    <td class="p-4">
                        @if($p->proof_path)
                            <a href="{{ asset('storage/' . $p->proof_path) }}" target="_blank" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded font-semibold text-slate-700">
                                🔍 Lihat Foto
                            </a>
                        @else
                            <span class="text-slate-400 italic">Belum diunggah</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <span class="px-2.5 py-0.5 rounded-full font-bold {{ $p->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($p->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                            {{ $p->status->label() }}
                        </span>
                    </td>
                    <td class="p-4 text-right space-x-1.5">
                        @if($p->status->value === 'pending' && $p->proof_path)
                            <form action="{{ route('admin.payments.verify', $p) }}" method="POST" class="inline" onsubmit="return confirm('Verifikasi pembayaran ini sebagai LUNAS?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-sm">
                                    Verifikasi Lunas
                                </button>
                            </form>
                            <form action="{{ route('admin.payments.reject', $p) }}" method="POST" class="inline" onsubmit="return confirm('Tolak pembayaran ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-2.5 py-1.5 text-rose-600 hover:bg-rose-50 font-bold rounded-lg">
                                    Tolak
                                </button>
                            </form>
                        @else
                            <span class="text-slate-400 text-[11px]">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-slate-400">Belum ada data pembayaran.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4 border-t border-slate-100">
        {{ $payments->links() }}
    </div>
</div>
@endsection
