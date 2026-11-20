@extends('layouts.app')

@section('title', 'Masuk ke Akun Anda — TripMate')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Masuk ke TripMate</h2>
        <p class="text-sm text-slate-500 mt-1">Gunakan akun Anda untuk melanjutkan pemesanan</p>
    </div>

    <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" id="password" required
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2">
                Ingat saya
            </label>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg hover:bg-blue-700 transition">
            Masuk Sekarang
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-slate-500">
        Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Daftar di sini</a>
    </div>
</div>
@endsection
