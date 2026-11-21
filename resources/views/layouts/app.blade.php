<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TripMate — Rental Mobil Liburan')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">TripMate</span>
                </a>
                <nav class="hidden md:flex space-x-6 text-sm font-medium">
                    <a href="{{ route('customer.destinations.index') }}" class="text-slate-600 hover:text-blue-600 transition">Destinasi Wisata</a>
                    <a href="{{ route('customer.vehicles.index') }}" class="text-slate-600 hover:text-blue-600 transition">Katalog Armada</a>
                    @auth
                        <a href="{{ route('customer.vacation-plans.create') }}" class="text-blue-600 font-semibold hover:text-blue-700">✨ Bantu Pilihkan Mobil</a>
                        <a href="{{ route('customer.vacation-plans.index') }}" class="text-slate-600 hover:text-blue-600 transition">Rencana Saya</a>
                        <a href="{{ route('customer.bookings.index') }}" class="text-slate-600 hover:text-blue-600 transition">Pesanan Saya</a>
                    @endauth
                </nav>
            </div>

            <div class="flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">Masuk</a>
                    <a href="{{ route('register') }}" class="text-sm font-medium bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Daftar</a>
                @else
                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="text-xs bg-amber-100 text-amber-800 font-semibold px-2.5 py-1 rounded">Panel Admin</a>
                    @endif
                    <span class="text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">Keluar</button>
                    </form>
                @endguest
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-md text-emerald-800 text-sm mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-md text-rose-800 text-sm mb-4">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-md text-rose-800 text-sm mb-4">
                <p class="font-semibold mb-1">Terdapat beberapa kesalahan input:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-12 py-8 text-center text-sm text-slate-500">
        <p>&copy; {{ date('Y') }} TripMate — Platform Rental Mobil Berbasis Preferensi Perjalanan.</p>
    </footer>
    @stack('scripts')
</body>
</html>
