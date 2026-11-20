@extends('layouts.app')

@section('title', 'Beri Ulasan Booking ' . $booking->booking_code . ' — TripMate')

@section('content')
<div class="max-w-md mx-auto my-8 bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Beri Ulasan & Rating</h1>
        <p class="text-xs text-slate-500 mt-1">
            Bagaimana pengalaman Anda menggunakan <strong>{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</strong>?
        </p>
    </div>

    <form action="{{ route('customer.bookings.review.store', $booking) }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Penilaian Bintang (1 - 5) <span class="text-rose-500">*</span></label>
            <div class="flex justify-center space-x-4 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                @for($i = 5; $i >= 1; $i--)
                    <label class="flex flex-col items-center cursor-pointer">
                        <input type="radio" name="rating" value="{{ $i }}" {{ old('rating', 5) == $i ? 'checked' : '' }}
                            class="w-4 h-4 text-amber-500 focus:ring-amber-500 mb-1">
                        <span class="text-sm font-bold text-slate-800">{{ $i }} ★</span>
                    </label>
                @endfor
            </div>
        </div>

        <div>
            <label for="comment" class="block text-xs font-bold text-slate-700 mb-1">Ulasan Pengalaman Anda</label>
            <textarea name="comment" id="comment" rows="4" placeholder="Ceritakan kenyamanan, kebersihan unit, atau performa mesin..."
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('comment') }}</textarea>
        </div>

        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm shadow transition">
            Kirim Ulasan
        </button>
    </form>
</div>
@endsection
