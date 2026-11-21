@extends('layouts.admin')

@section('title', 'Dashboard Operasional — TripMate')
@section('page_title', 'Ringkasan Operasional')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-container {
        z-index: 10 !important;
        font-family: inherit;
    }
</style>
@endpush

@section('content')
<!-- Stats Cards (Grid 1 -> 2 -> 4) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Armada Mobil</span>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['total_vehicles'] }}</div>
        </div>
        <span class="text-xs text-emerald-600 font-semibold mt-2 block">{{ $stats['active_vehicles'] }} Unit Aktif Disewakan</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Booking Menunggu Bayar</span>
            <div class="text-3xl font-extrabold text-amber-600 mt-2">{{ $stats['pending_bookings'] }}</div>
        </div>
        <span class="text-xs text-slate-400 mt-2 block">Hold maksimal 24 jam</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Verifikasi Pembayaran</span>
            <div class="text-3xl font-extrabold text-blue-600 mt-2">{{ $stats['pending_payments'] }}</div>
        </div>
        <span class="text-xs text-slate-400 mt-2 block">Menunggu verifikasi bukti transfer</span>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Pendapatan Terverifikasi</span>
            <div class="text-xl sm:text-2xl font-black text-emerald-600 mt-2 whitespace-nowrap">
                Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
            </div>
        </div>
        <span class="text-xs text-slate-400 mt-2 block">Dari transaksi berstatus Lunas</span>
    </div>
</div>

<!-- Operational Monitoring Map for All Active Bookings -->
<div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
        <div>
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span>🗺️</span> Peta Monitoring Titik Serah Terima Booking Aktif
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan lokasi penjemputan armada yang sedang pending, confirmed, atau ongoing</p>
        </div>
        <div class="flex items-center gap-3 text-xs text-slate-500">
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Pending</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Confirmed</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Ongoing</span>
        </div>
    </div>

    <div id="admin-monitoring-map" class="h-80 w-full rounded-xl border border-slate-200 shadow-inner"></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Pending Payments Queue -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Perlu Verifikasi Pembayaran</h3>
                <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-blue-600 hover:underline">Semua →</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($pendingPayments as $p)
                    <div class="py-3 flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <strong class="text-slate-900 block font-mono">{{ $p->booking->booking_code }}</strong>
                            <span class="text-slate-600 block truncate">{{ $p->booking->user->name }} • {{ $p->booking->vehicle->brand }} {{ $p->booking->vehicle->model }}</span>
                            <span class="block text-blue-600 font-bold mt-0.5 whitespace-nowrap">Rp {{ number_format((float) $p->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0">
                            @if($p->proof_path)
                                <a href="{{ asset('storage/' . $p->proof_path) }}" target="_blank" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs whitespace-nowrap">
                                    Bukti
                                </a>
                            @endif
                            <form action="{{ route('admin.payments.verify', $p) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-xs whitespace-nowrap">
                                    Verifikasi
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-6 text-center">Tidak ada pembayaran yang menunggu verifikasi.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Booking Terbaru</h3>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Semua →</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentBookings as $b)
                    <div class="py-3 flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center space-x-2">
                                <strong class="text-slate-900 font-mono">{{ $b->booking_code }}</strong>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold whitespace-nowrap {{ $b->status->value === 'confirmed' ? 'bg-blue-100 text-blue-800' : ($b->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : ($b->status->value === 'ongoing' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700')) }}">
                                    {{ $b->status->label() }}
                                </span>
                            </div>
                            <span class="text-slate-600 block truncate mt-0.5">{{ $b->user->name }} • {{ $b->vehicle->brand }} {{ $b->vehicle->model }}</span>
                            <span class="text-slate-400 block text-[11px] whitespace-nowrap">{{ $b->pickup_datetime->format('d M, H:i') }} - {{ $b->return_datetime->format('d M Y, H:i') }}</span>
                        </div>
                        <a href="{{ route('admin.bookings.show', $b) }}" class="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold rounded-lg shrink-0 whitespace-nowrap">
                            Detail
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-6 text-center">Belum ada transaksi booking.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const monitoringMap = L.map('admin-monitoring-map').setView([-7.7956, 110.3695], 9);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(monitoringMap);

    const bounds = [];
    const bookings = @json($activeMapBookings);

    if (bookings && bookings.length > 0) {
        bookings.forEach(function (b) {
            if (b.lat && b.lng) {
                const marker = L.marker([b.lat, b.lng]).addTo(monitoringMap);
                marker.bindPopup(`
                    <div style="font-family: inherit; font-size: 12px; min-width: 180px;">
                        <strong style="font-family: monospace; color: #2563eb;">${b.code}</strong>
                        <div style="font-weight: 700; color: #0f172a; margin-top: 2px;">${b.vehicle}</div>
                        <div style="color: #64748b; font-size: 11px;">Customer: ${b.customer}</div>
                        <div style="color: #475569; font-size: 11px; margin-top: 4px;">📍 ${b.address}</div>
                        <div style="margin-top: 6px;">
                            <a href="${b.url}" style="color: #2563eb; font-weight: 600; text-decoration: underline;">Buka Detail Booking →</a>
                        </div>
                    </div>
                `);
                bounds.push([b.lat, b.lng]);
            }
        });

        if (bounds.length > 0) {
            monitoringMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 14 });
        }
    }
});
</script>
@endpush
