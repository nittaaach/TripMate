<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TripMate — Rental Mobil & Rekomendasi Perjalanan')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&display=swap" rel="stylesheet"/>
    <link href="{{ asset('landing/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('landing/css/aos.css') }}" rel="stylesheet"/>
    <link href="{{ asset('landing/css/swiper-bundle.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('landing/css/all.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('landing/css/magnific-popup.css') }}"/>
    <link rel="stylesheet" href="{{ asset('landing/css/style.css') }}"/>
    @stack('styles')
</head>
<body>
    <!-- TOP BAR -->
    <div id="topbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="top-contact d-flex flex-wrap">
                    <span><i class="fas fa-phone-alt"></i>+62 812-3456-7890</span>
                    <span><i class="fas fa-envelope"></i>halo@tripmate.id</span>
                    <span><i class="fas fa-map-marker-alt"></i>Depok &amp; Jabodetabek</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="ttag"><i class="fas fa-car me-1"></i>Rekomendasi Mobil Cerdas!</span>
                    <div class="tsoc">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg" id="nav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <div class="blogo">
                    <div class="bico"><i class="fas fa-car-side"></i></div>
                    <div>
                        <div class="bname">Trip<span>Mate</span></div>
                        <div class="bsub">Smart Car Rental</div>
                    </div>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
                <i class="fas fa-bars" style="color:var(--primary);font-size:1.35rem;"></i>
            </button>
            <div class="collapse navbar-collapse" id="navmenu">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="{{ url('/#hero') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#about') }}">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#menu') }}">Armada</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#destinasi') }}">Destinasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#testimonials') }}">Testimoni</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('customer.vacation-plans.index') }}">Rencana Saya</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('customer.bookings.index') }}">Pesanan</a></li>
                    @endauth
                </ul>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button id="navSearchBtn" title="Cari Armada"><i class="fas fa-search"></i></button>
                    @guest
                        <a href="{{ route('login') }}" class="nav-link" style="font-weight:600;">Masuk</a>
                        <a href="{{ route('register') }}" class="nav-link nav-cta"><i class="fas fa-user-plus me-1"></i>Daftar</a>
                    @else
                        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <a href="{{ route('admin.dashboard') }}" class="nav-link" style="font-weight:600;color:var(--secondary);">Admin</a>
                        @endif
                        <a href="{{ route('customer.vacation-plans.create') }}" class="nav-link nav-cta"><i class="fas fa-magic me-1"></i>Rekomendasi</a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Keluar</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- SEARCH OVERLAY -->
    <div id="searchOv">
        <button class="sovclose" id="searchClose"><i class="fas fa-times"></i></button>
        <div class="sovbox">
            <h4>Mau cari mobil apa untuk liburan?</h4>
            <form action="{{ route('customer.vehicles.index') }}" method="GET" class="sovinput">
                <input type="text" name="search" id="searchInput" placeholder="Cari Avanza, Innova, SUV, Brio..." autocomplete="off"/>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div class="sovtrend mt-3">
                <p><i class="fas fa-fire me-1" style="color:var(--secondary);"></i>Pencarian Populer</p>
                <a href="{{ route('customer.vehicles.index', ['category' => 'suv']) }}" class="ttag text-decoration-none">SUV Pegunungan</a>
                <a href="{{ route('customer.vehicles.index', ['category' => 'mpv']) }}" class="ttag text-decoration-none">MPV Keluarga</a>
                <a href="{{ route('customer.vehicles.index', ['category' => 'city_car']) }}" class="ttag text-decoration-none">City Car</a>
                <a href="{{ route('customer.destinations.index') }}" class="ttag text-decoration-none">Destinasi Wisata</a>
            </div>
        </div>
    </div>

    @yield('content')

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="fnm">Trip<span>Mate</span></div>
                    <p class="fdesc">Platform rental mobil berbasis rekomendasi perjalanan cerdas. Sesuaikan armada dengan medan destinasi, kapasitas penumpang, dan anggaran liburanmu.</p>
                    <div class="fsoc">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <div class="ftit">Navigasi</div>
                    <ul class="flinks ps-0">
                        <li><a href="{{ url('/#hero') }}"><i class="fas fa-chevron-right"></i>Beranda</a></li>
                        <li><a href="{{ url('/#about') }}"><i class="fas fa-chevron-right"></i>Tentang Kami</a></li>
                        <li><a href="{{ route('customer.vehicles.index') }}"><i class="fas fa-chevron-right"></i>Katalog Armada</a></li>
                        <li><a href="{{ route('customer.destinations.index') }}"><i class="fas fa-chevron-right"></i>Destinasi</a></li>
                        <li><a href="{{ route('customer.vacation-plans.create') }}"><i class="fas fa-chevron-right"></i>Rekomendasi</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <div class="ftit">Kategori Mobil</div>
                    <ul class="flinks ps-0">
                        <li><a href="{{ route('customer.vehicles.index') }}"><i class="fas fa-chevron-right"></i>Semua Armada</a></li>
                        <li><a href="{{ route('customer.vehicles.index', ['category' => 'suv']) }}"><i class="fas fa-chevron-right"></i>SUV</a></li>
                        <li><a href="{{ route('customer.vehicles.index', ['category' => 'mpv']) }}"><i class="fas fa-chevron-right"></i>MPV Keluarga</a></li>
                        <li><a href="{{ route('customer.vehicles.index', ['category' => 'city_car']) }}"><i class="fas fa-chevron-right"></i>City Car</a></li>
                        <li><a href="{{ route('customer.vehicles.index', ['category' => 'sedan']) }}"><i class="fas fa-chevron-right"></i>Sedan</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <div class="ftit">Hubungi Kami</div>
                    <div class="fci">
                        <div class="fciico"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="fciinfo"><strong>Alamat</strong>Depok, Jawa Barat — Indonesia</div>
                    </div>
                    <div class="fci">
                        <div class="fciico"><i class="fas fa-phone-alt"></i></div>
                        <div class="fciinfo"><strong>Telepon / WA</strong>+62 812-3456-7890</div>
                    </div>
                    <div class="fci">
                        <div class="fciico"><i class="fas fa-envelope"></i></div>
                        <div class="fciinfo"><strong>Email</strong>halo@tripmate.id</div>
                    </div>
                    <div class="fci">
                        <div class="fciico"><i class="fas fa-clock"></i></div>
                        <div class="fciinfo"><strong>Layanan</strong>Setiap Hari: 07.00 - 22.00 WIB</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fbot">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <p>&copy; {{ date('Y') }} <span>TripMate</span>. Platform Rental Mobil &amp; Rekomendasi Perjalanan.</p>
                    <div>
                        <a href="{{ route('customer.vehicles.index') }}">Armada</a>
                        <a href="{{ route('customer.destinations.index') }}">Destinasi</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <button id="btt" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="fas fa-chevron-up"></i></button>

    <script src="{{ asset('landing/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('landing/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('landing/js/aos.js') }}"></script>
    <script src="{{ asset('landing/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('landing/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('landing/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
