@extends('layouts.landing')

@section('title', 'TripMate — Rental Mobil Berbasis Preferensi Liburan')

@section('content')
    <!-- HERO -->
    <section id="hero">
        <div class="hs hs1"></div>
        <div class="hs hs2"></div>
        <div class="hbgtxt">RENTAL</div>
        <div class="container">
            <div class="row align-items-center g-5" style="min-height:88vh;">
                <div class="col-lg-6">
                    <div class="hbadge">
                        <div class="hbi"><i class="fas fa-star"></i></div>
                        <span>Platform Rental Mobil Cerdas di Indonesia</span>
                    </div>
                    <h1 class="htitle">Temukan <span class="hl">Mobil Ideal</span><br/>untuk Setiap Perjalanan</h1>
                    <p class="hdesc">Bingung memilih mobil yang pas untuk medan pegunungan, pantai, atau perjalanan keluarga? Sistem rekomendasi TripMate menyaring armada terbaik sesuai rute, kapasitas, dan anggaranmu.</p>
                    <div class="d-flex flex-wrap gap-3 mb-2">
                        <a href="{{ route('customer.vacation-plans.create') }}" class="btn-red"><i class="fas fa-magic"></i>Bantu Pilihkan Mobil</a>
                        <a href="{{ route('customer.vehicles.index') }}" class="btn-play text-decoration-none">
                            <div class="pico"><i class="fas fa-car"></i></div>
                            <span>Lihat Semua Armada</span>
                        </a>
                    </div>
                    <div class="hstats d-flex gap-3 flex-wrap mt-4">
                        <div class="hstat"><span class="snum">850<em>+</em></span><small>Pelanggan Puas</small></div>
                        <div class="sdiv"></div>
                        <div class="hstat"><span class="snum">{{ $featuredVehicles->count() }}<em>+</em></span><small>Armada Pilihan</small></div>
                        <div class="sdiv"></div>
                        <div class="hstat"><span class="snum">{{ $destinations->count() }}<em>+</em></span><small>Destinasi Wisata</small></div>
                        <div class="sdiv"></div>
                        <div class="hstat"><span class="snum">24<em>/7</em></span><small>Dukungan Jalan</small></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="position:relative;text-align:center;">
                        <div class="hcircle">
                            <img src="{{ asset('landing/img/hero-car.jpg') }}" alt="Armada TripMate"/>
                        </div>
                        <div class="fcard fc1">
                            <div class="fcoi r"><i class="fas fa-route"></i></div>
                            <div><span class="fcnum">Smart Match</span><span class="fcsm">Sesuai medan rute</span></div>
                        </div>
                        <div class="fcard fc2">
                            <div class="fcoi y"><i class="fas fa-star"></i></div>
                            <div><span class="fcnum">4.9/5</span><span class="fcsm">Unit terawat prima</span></div>
                        </div>
                        <div class="fcard fc3">
                            <div class="fcoi g"><i class="fas fa-shield-alt"></i></div>
                            <div><span class="fcnum">Lepas Kunci</span><span class="fcsm">Atau dengan driver</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MARQUEE -->
    <div class="mqsec">
        <div class="mqtrack">
            <div class="mqitem"><i class="fas fa-circle"></i>SUV Tangguh Pegunungan</div>
            <div class="mqitem"><i class="fas fa-circle"></i>MPV Nyaman Keluarga</div>
            <div class="mqitem"><i class="fas fa-circle"></i>City Car Hemat BBM</div>
            <div class="mqitem"><i class="fas fa-circle"></i>Rekomendasi Rute Otomatis</div>
            <div class="mqitem"><i class="fas fa-circle"></i>Lepas Kunci &amp; Driver</div>
            <div class="mqitem"><i class="fas fa-circle"></i>Tarif Transparan Tanpa Biaya Tersembunyi</div>
            <div class="mqitem"><i class="fas fa-circle"></i>SUV Tangguh Pegunungan</div>
            <div class="mqitem"><i class="fas fa-circle"></i>MPV Nyaman Keluarga</div>
            <div class="mqitem"><i class="fas fa-circle"></i>City Car Hemat BBM</div>
            <div class="mqitem"><i class="fas fa-circle"></i>Rekomendasi Rute Otomatis</div>
        </div>
    </div>

    <!-- CATEGORY -->
    <section id="category">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">Pilihan Tipe Kendaraan</span>
                <h2 class="stitle">Jelajahi Berdasarkan <span>Kategori</span></h2>
                <div class="sline"></div>
                <p class="sdesc mx-auto" style="max-width:500px;">Dari mobil kota yang irit hingga SUV tangguh untuk jalan menanjak—pilih tipe kendaraan sesuai kebutuhan liburanmu.</p>
            </div>
            <div class="row g-3 justify-content-center">
                @php
                    $cats = [
                        ['slug' => 'all', 'label' => 'Semua Tipe', 'sub' => 'Semua unit', 'icon' => 'fa-th-large'],
                        ['slug' => 'mpv', 'label' => 'MPV Keluarga', 'sub' => '6–7 Kursi', 'icon' => 'fa-shuttle-van'],
                        ['slug' => 'suv', 'label' => 'SUV Tangguh', 'sub' => 'Medan berat', 'icon' => 'fa-truck-pickup'],
                        ['slug' => 'city_car', 'label' => 'City Car', 'sub' => 'Hemat BBM', 'icon' => 'fa-car-side'],
                        ['slug' => 'sedan', 'label' => 'Sedan', 'sub' => 'Nyaman & elegan', 'icon' => 'fa-car'],
                        ['slug' => 'ev', 'label' => 'Listrik / Hybrid', 'sub' => 'Ramah lingkungan', 'icon' => 'fa-charging-station'],
                    ];
                @endphp
                @foreach($cats as $i => $cat)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2" data-aos="zoom-in" data-aos-delay="{{ $i * 60 }}">
                        <div class="catcard {{ $i === 0 ? 'active' : '' }}" data-filter="{{ $cat['slug'] }}">
                            <div class="d-flex align-items-center justify-content-center mx-auto mb-2" style="width:62px;height:62px;border-radius:50%;background:rgba(232,40,26,.1);color:var(--primary);font-size:1.5rem;">
                                <i class="fas {{ $cat['icon'] }}"></i>
                            </div>
                            <div class="catnm">{{ $cat['label'] }}</div>
                            <div class="catct">{{ $cat['sub'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="astack">
                        <div class="aexp"><span class="anum">100%</span><small>Armada<br/>Terawat</small></div>
                        <div class="amain"><img src="{{ asset('landing/img/about-car.jpg') }}" alt="Perjalanan Bersama TripMate"/></div>
                        <div class="asm"><img src="{{ asset('landing/img/hero-car.jpg') }}" alt="SUV TripMate"/></div>
                    </div>
                </div>
                <div class="col-lg-7" data-aos="fade-left">
                    <span class="slbl">Kenapa TripMate</span>
                    <h2 class="stitle text-start">Sewa Mobil Lebih Tepat<br/>dengan <span>Rekomendasi Cerdas</span></h2>
                    <div class="sline lft"></div>
                    <p class="sdesc mb-4">TripMate tidak sekadar menyewakan kendaraan. Kami mencocokkan kondisi jalan destinasi, jarak tempuh, jumlah penumpang, hingga anggaran harianmu dengan armada terbaik yang tersedia.</p>
                    <div class="mb-4">
                        <div class="fti">
                            <div class="ftico r"><i class="fas fa-sliders-h"></i></div>
                            <div>
                                <h6>Skor Kecocokan Otomatis</h6>
                                <p>Sistem menghitung kecocokan jarak, kapasitas bagasi, *ground clearance*, dan bujet per hari.</p>
                            </div>
                        </div>
                        <div class="fti">
                            <div class="ftico y"><i class="fas fa-tools"></i></div>
                            <div>
                                <h6>Unit Selalu Servis Berkala</h6>
                                <p>Setiap kendaraan memiliki jadwal perawatan rutin untuk menjamin keamanan di perjalanan.</p>
                            </div>
                        </div>
                        <div class="fti">
                            <div class="ftico g"><i class="fas fa-bolt"></i></div>
                            <div>
                                <h6>Pemesanan Cepat &amp; Transparan</h6>
                                <p>Tarif harian, deposit, serta opsi dengan atau tanpa pengemudi langsung terlihat jelas sebelum bayar.</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('customer.vacation-plans.create') }}" class="btn-red"><i class="fas fa-magic"></i>Coba Rekomendasi Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ARMADA PILIHAN (MENU) -->
    <section id="menu">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">Armada Tersedia</span>
                <h2 class="stitle">Armada Pilihan <span>TripMate</span></h2>
                <div class="sline"></div>
            </div>

            <div class="text-center mb-4" data-aos="fade-up">
                <button class="filtbtn active" data-f="all">Semua</button>
                @foreach($featuredVehicles->pluck('category')->unique() as $catEnum)
                    <button class="filtbtn" data-f="{{ strtolower($catEnum->value) }}">{{ $catEnum->label() }}</button>
                @endforeach
            </div>

            <div class="row g-4" id="mgrid">
                @forelse($featuredVehicles as $vehicle)
                    <div class="col-sm-6 col-lg-4 mwrap" data-c="{{ strtolower($vehicle->category->value) }}" data-aos="fade-up">
                        <div class="mcard">
                            <div class="mimg">
                                @if($vehicle->primaryImage)
                                    <img src="{{ asset('storage/' . $vehicle->primaryImage->image_path) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}"/>
                                @else
                                    <img src="{{ asset('landing/img/hero-car.jpg') }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}"/>
                                @endif
                                <div class="mbdg hot"><i class="fas fa-car"></i> {{ $vehicle->category->label() }}</div>
                            </div>
                            <div class="mbody">
                                <div class="mcat">{{ $vehicle->brand }} • {{ $vehicle->year ?? 'Terbaru' }}</div>
                                <div class="mtit">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                <div class="mdesc">
                                    {{ $vehicle->seats }} Kursi • {{ $vehicle->transmission->label() }} • {{ $vehicle->fuel_type->label() }}
                                    @if($vehicle->has_ac) • AC @endif
                                </div>
                                <div class="mfoot">
                                    <div>
                                        <div class="mprice">Rp {{ number_format((float) $vehicle->daily_rate, 0, ',', '.') }} <small style="text-decoration:none;">/hari</small></div>
                                    </div>
                                    <a href="{{ route('customer.vehicles.show', $vehicle) }}" class="madd text-decoration-none" title="Detail &amp; Sewa">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Belum ada armada aktif yang tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('customer.vehicles.index') }}" class="btn-red"><i class="fas fa-car-side"></i>Lihat Katalog Lengkap</a>
            </div>
        </div>
    </section>

    <!-- SPECIAL CTA: REKOMENDASI PERJALANAN -->
    <section id="special">
        <div class="spbg"></div>
        <div class="container" style="position:relative;z-index:2;">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="sptag"><i class="fas fa-magic me-1"></i>Fitur Unggulan TripMate</div>
                    <h2 class="sptitle">Bingung Pilih Mobil<br/>untuk Rencana<br/><span>Liburan</span> Kamu?</h2>
                    <p class="spdesc">Masukkan tujuan wisata, tanggal berangkat, jumlah penumpang, dan anggaran harianmu. Sistem akan langsung merekomendasikan mobil yang paling cocok!</p>
                    <div class="cdwrap">
                        <div class="cditem"><span class="cdnum" id="cdH">08</span><span class="cdlbl">Jam</span></div>
                        <div class="cditem"><span class="cdnum" id="cdM">45</span><span class="cdlbl">Menit</span></div>
                        <div class="cditem"><span class="cdnum" id="cdS">30</span><span class="cdlbl">Detik</span></div>
                    </div>
                    <a href="{{ route('customer.vacation-plans.create') }}" class="btn-red"><i class="fas fa-route"></i>Buat Rencana Liburan</a>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="spimgw">
                        <div class="spglow"></div>
                        <img src="{{ asset('landing/img/about-car.jpg') }}" alt="Rekomendasi TripMate"/>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DESTINASI WISATA PILIHAN -->
    <section id="destinasi" style="padding:90px 0;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">Inspirasi Perjalanan</span>
                <h2 class="stitle">Destinasi Wisata <span>Pilihan</span></h2>
                <div class="sline"></div>
                <p class="sdesc mx-auto" style="max-width:520px;">Pilih destinasi tujuanmu dan lihat rekomendasi armada yang paling aman untuk kondisi jalannya.</p>
            </div>
            <div class="row g-4">
                @forelse($destinations as $dest)
                    <div class="col-md-6 col-lg-4" data-aos="fade-up">
                        <div class="mcard h-100">
                            <div class="mbody d-flex flex-column justify-content-between h-100">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="mcat mb-0"><i class="fas fa-map-marker-alt me-1"></i>{{ $dest->city }}, {{ $dest->province }}</span>
                                        <span class="badge rounded-pill text-bg-warning">{{ $dest->terrain_type->label() }}</span>
                                    </div>
                                    <div class="mtit">{{ $dest->name }}</div>
                                    <p class="mdesc">{{ \Illuminate\Support\Str::limit($dest->description ?? 'Destinasi wisata menarik siap dieksplorasi bersama TripMate.', 110) }}</p>
                                </div>
                                <div class="mfoot mt-3 pt-3 border-top">
                                    <small class="text-muted">Jalan: <strong>{{ $dest->road_condition->label() }}</strong></small>
                                    <a href="{{ route('customer.vacation-plans.create', ['destination_id' => $dest->id]) }}" class="btn btn-sm btn-danger rounded-pill px-3">
                                        Rencanakan <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4">
                        <p class="text-muted">Belum ada destinasi aktif.</p>
                    </div>
                @endforelse
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('customer.destinations.index') }}" class="btn-red"><i class="fas fa-map-marked-alt"></i>Lihat Semua Destinasi</a>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section id="testimonials">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">Kata Mereka</span>
                <h2 class="stitle">Testimoni <span>Pelanggan</span></h2>
                <div class="sline"></div>
            </div>
            <div class="swiper tesSwiper" data-aos="fade-up">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="tescard">
                            <div class="tesq">"</div>
                            <div class="tess"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="testxt">Fitur rekomendasinya membantu banget waktu liburan Bersama Teman ke Puncak. Langsung dipilihkan SUV yang kuat menanjak dan muat koper banyak cihuyy.</p>
                            <div class="tesauth">
                                <img src="{{ asset('landing/img/testimonial/5.jpg') }}" alt=""/>
                                <div>
                                    <div class="tesnm">Marvie</div>
                                    <div class="tesrl">Pelanggan TripMate</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="tescard">
                            <div class="tesq">"</div>
                            <div class="tess"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="testxt">Unit bersih, AC dingin, dan proses booking sampai upload bukti bayar sangat cepat. Tidak perlu bingung tanya sana-sini.</p>
                            <div class="tesauth">
                                <img src="{{ asset('landing/img/testimonial/2.jpg') }}" alt=""/>
                                <div>
                                    <div class="tesnm">Budi Santoso</div>
                                    <div class="tesrl">Traveler</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="tescard">
                            <div class="tesq">"</div>
                            <div class="tess"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="testxt">Transparan dari awal, tarif harian dan deposit jelas. Sangat memudahkan untuk trip bareng teman-teman kampus.</p>
                            <div class="tesauth">
                                <img src="{{ asset('landing/img/testimonial/3.jpg') }}" alt=""/>
                                <div>
                                    <div class="tesnm">Nadia Putri</div>
                                    <div class="tesrl">Mahasiswi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination mt-4" style="position:static;"></div>
            </div>
        </div>
    </section>
@endsection
