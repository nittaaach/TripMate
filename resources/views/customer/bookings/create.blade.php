@extends('layouts.app')

@section('title', 'Form Pemesanan Rental — TripMate')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('customer.vehicles.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
            ← Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Booking Form (2 Columns) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Formulir Pemesanan</h1>
                <p class="text-xs text-slate-500 mb-6">Lengkapi data jadwal dan lokasi serah terima kendaraan</p>

                <form action="{{ route('customer.bookings.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                    @if($plan)
                        <input type="hidden" name="vacation_plan_id" value="{{ $plan->id }}">
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="pickup_datetime" class="block text-xs font-bold text-slate-700 mb-1">
                                Waktu Penjemputan / Ambil <span class="text-rose-500">*</span>
                            </label>
                            <input type="datetime-local" name="pickup_datetime" id="pickup_datetime"
                                value="{{ old('pickup_datetime', $pickupDatetime->format('Y-m-d\TH:i')) }}" required
                                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="return_datetime" class="block text-xs font-bold text-slate-700 mb-1">
                                Waktu Pengembalian <span class="text-rose-500">*</span>
                            </label>
                            <input type="datetime-local" name="return_datetime" id="return_datetime"
                                value="{{ old('return_datetime', $returnDatetime->format('Y-m-d\TH:i')) }}" required
                                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="pickup_address" class="block text-xs font-bold text-slate-700 mb-1">
                            Alamat Lokasi Penjemputan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="pickup_address" id="pickup_address" rows="2" required placeholder="Alamat hotel, bandara, stasiun, atau rumah Anda"
                            class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('pickup_address', $pickupAddress) }}</textarea>
                    </div>

                    <div>
                        <label for="return_address" class="block text-xs font-bold text-slate-700 mb-1">
                            Alamat Lokasi Pengembalian <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="return_address" id="return_address" rows="2" required placeholder="Alamat pengembalian mobil"
                            class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('return_address', $returnAddress) }}</textarea>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                            <input type="checkbox" name="needs_driver" value="1" {{ old('needs_driver', $needsDriver) ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-bold text-slate-800">Gunakan Layanan Sopir (+ Rp 150.000 / hari)</span>
                                <span class="text-xs text-slate-500 block">Sopir profesional berpengalaman siap mengantar Anda selama sewa.</span>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" id="notes" rows="2" placeholder="Informasi khusus untuk staf serah terima"
                            class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('notes') }}</textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-400">Pemesanan aman dengan sistem anti double-booking.</span>
                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition">
                            Konfirmasi & Lanjut Pembayaran →
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Vehicle & Pricing Summary (1 Column) -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Ringkasan Armada</h3>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-16 h-12 bg-slate-100 rounded-lg overflow-hidden flex items-center justify-center">
                        @if($vehicle->primaryImage)
                            <img src="{{ asset('storage/' . $vehicle->primaryImage->image_path) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-xl">🚗</span>
                        @endif
                    </div>
                    <div>
                        <strong class="text-sm text-slate-900 block">{{ $vehicle->brand }} {{ $vehicle->model }}</strong>
                        <span class="text-xs text-slate-500">{{ $vehicle->category->label() }} • {{ $vehicle->seats }} Kursi</span>
                    </div>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl text-xs space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tarif Dasar / Hari:</span>
                        <strong class="text-slate-800">Rp {{ number_format($pricing['daily_rate_snapshot'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Durasi Sewa:</span>
                        <strong class="text-slate-800">{{ $pricing['rental_days'] }} Hari</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Subtotal Sewa:</span>
                        <strong class="text-slate-800">Rp {{ number_format($pricing['subtotal'], 0, ',', '.') }}</strong>
                    </div>
                    @if($pricing['additional_fee'] > 0)
                        <div class="flex justify-between text-blue-700">
                            <span>Biaya Sopir ({{ $pricing['rental_days'] }} hari):</span>
                            <strong>+ Rp {{ number_format($pricing['additional_fee'], 0, ',', '.') }}</strong>
                        </div>
                    @endif
                    <div class="flex justify-between text-slate-500 pt-2 border-t border-slate-200">
                        <span>Deposit Jaminan (Kembali):</span>
                        <strong class="text-slate-700">Rp {{ number_format($pricing['deposit_amount'], 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800">Total Pembayaran:</span>
                    <span class="text-xl font-extrabold text-blue-600">
                        Rp {{ number_format($pricing['total_amount'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
