<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel — TripMate')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col min-h-screen">
        <div class="p-6 border-b border-slate-800">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight text-white flex items-center space-x-2">
                <span>TripMate <span class="text-blue-400 font-normal text-sm">Staff</span></span>
            </a>
            <p class="text-xs text-slate-400 mt-1">{{ auth()->user()->role->label() }}</p>
        </div>

        <nav class="flex-grow p-4 space-y-1.5 text-sm font-medium">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('admin.bookings.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                Kelola Booking
            </a>
            <a href="{{ route('admin.payments.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('admin.payments.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                Verifikasi Pembayaran
            </a>
            <a href="{{ route('admin.maintenance.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('admin.maintenance.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                Jadwal Perawatan (Maintenance)
            </a>

            @if(auth()->user()->isAdmin())
                <div class="pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider px-3">Master Data (Admin)</div>
                <a href="{{ route('admin.vehicles.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('admin.vehicles.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    Armada Kendaraan
                </a>
                <a href="{{ route('admin.destinations.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('admin.destinations.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    Destinasi Wisata
                </a>
            @endif
        </nav>

        <div class="p-4 border-t border-slate-800 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xs text-blue-400 hover:underline">← Ke Situs Utama</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-red-400 hover:underline">Keluar</button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col min-w-0">
        <!-- Topbar -->
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-8">
            <h1 class="text-lg font-semibold text-slate-800">@yield('page_title', 'Dashboard')</h1>
            <div class="text-sm text-slate-600">
                Halo, <span class="font-semibold text-slate-900">{{ auth()->user()->name }}</span>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="px-8 mt-6">
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

        <main class="p-8 flex-grow">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
