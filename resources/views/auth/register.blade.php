@extends('layouts.app')

@section('title', 'Daftar Akun Baru — TripMate')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Buat Akun TripMate</h2>
        <p class="text-sm text-slate-500 mt-1">Daftar untuk membuat rencana perjalanan & reservasi mobil</p>
    </div>

    <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Nomor WhatsApp / HP</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Contoh: 08123456789"
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" id="password" required
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg hover:bg-blue-700 transition">
            Daftar Sekarang
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-slate-500">
        Sudah memiliki akun? <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Masuk di sini</a>
    </div>
</div>
@endsection
