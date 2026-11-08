@extends('layouts.app')

@section('content')
@php
$categoryImages = [
    'Coffee' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600&auto=format&fit=crop&q=80',
    'Tea' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=600&auto=format&fit=crop&q=80',
    'Food' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80',
    'Bakery/Pastry' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&auto=format&fit=crop&q=80',
    'Snacks' => 'https://images.unsplash.com/photo-1599490659213-e2b9527bb087?w=600&auto=format&fit=crop&q=80',
    'Bundling/Paket' => 'https://images.unsplash.com/photo-1513267048331-5611cad2134b?w=600&auto=format&fit=crop&q=80',
    'default' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600&auto=format&fit=crop&q=80'
];
@endphp

<div class="space-y-24 pb-16">
    <!-- Hero Section -->
    <section class="relative rounded-[2.5rem] overflow-hidden shadow-2xl border border-brand-500/10 dark:border-brand-500/5">
        <!-- Hero Background -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1498804103079-a6351b050096?w=1600&auto=format&fit=crop&q=80');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/70 to-transparent dark:from-black/95 dark:via-black/85"></div>
        
        <!-- Hero Content -->
        <div class="relative z-10 max-w-4xl px-5 py-16 sm:px-8 sm:py-24 md:py-32 md:px-16 text-white space-y-6 sm:space-y-8 animate-fade-in-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-500/20 border border-brand-500/30 text-brand-300 text-xs sm:text-sm font-semibold tracking-wide">
                <i class="fa-solid fa-star text-xs"></i>
                The Ultimate Cafe Experience
            </div>
            
            <h1 class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight leading-tight">
                Temukan Kehangatan <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-400 via-brand-500 to-amber-500">
                    Kopi & Cerita Anda
                </span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-300 max-w-2xl font-light leading-relaxed">
                Nikmati perpaduan biji kopi pilihan 100% Arabika & Robusta, suasana kerja yang tenang, serta hidangan pastry lezat yang dipanggang segar setiap hari.
            </p>
            
            <div class="flex flex-wrap gap-4 pt-4">
                <a href="#menu-section" class="px-8 py-4 bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white font-bold rounded-2xl shadow-lg shadow-brand-500/20 transition-all duration-300 hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-mug-hot"></i> Jelajahi Menu
                </a>
                <a href="#about-section" class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-medium rounded-2xl border border-white/20 transition-all duration-300 hover:-translate-y-0.5 flex items-center gap-2 backdrop-blur-md">
                    Pelajari Selengkapnya
                </a>
            </div>
        </div>
    </section>

    <!-- Highlights Section -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-luxury p-8 rounded-3xl border border-brand-500/10 dark:border-brand-500/5 flex flex-col gap-4 group hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-brand-500/10 text-brand-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-seedling"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Biji Kopi Premium</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Disajikan dari biji kopi lokal pilihan berkualitas tinggi yang disangrai dengan presisi oleh roaster ahli kami.
            </p>
        </div>

        <div class="glass-luxury p-8 rounded-3xl border border-brand-500/10 dark:border-brand-500/5 flex flex-col gap-4 group hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-brand-500/10 text-brand-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-wifi"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Workspace Nyaman</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Dilengkapi dengan Wi-Fi super cepat, stopkontak melimpah, dan kursi ergonomis yang ideal untuk bekerja/belajar.
            </p>
        </div>

        <div class="glass-luxury p-8 rounded-3xl border border-brand-500/10 dark:border-brand-500/5 flex flex-col gap-4 group hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-brand-500/10 text-brand-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-bread-slice"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Pastry Segar</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Croissant dan kue lezat kami dipanggang langsung di dapur kami setiap pagi untuk menjaga cita rasa premium.
            </p>
        </div>

        <div class="glass-luxury p-8 rounded-3xl border border-brand-500/10 dark:border-brand-500/5 flex flex-col gap-4 group hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-brand-500/10 text-brand-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-clock"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Buka Setiap Hari</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Kami siap menyambut Anda dari pagi hari untuk secangkir kopi pembuka hari hingga malam hari untuk bersantai bersama teman.
            </p>
        </div>
    </section>

    <!-- About Section -->
    <section id="about-section" class="scroll-mt-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-500 text-sm font-semibold tracking-wide">
                Tentang Ngafe
            </div>
            <h2 class="text-3xl md:text-5xl font-extrabold text-slate-800 dark:text-white leading-tight">
                Lebih dari Sekedar <br>Secangkir Kopi.
            </h2>
            <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-light">
                Di Ngafe, kami percaya bahwa setiap cangkir kopi membawa obrolan, inspirasi, dan koneksi baru. Kami berdedikasi untuk memberikan kualitas pelayanan terbaik dengan atmosfer yang hangat dan modern. Kami merupakan tempat berkumpulnya ide, cerita, dan energi positif.
            </p>
            <div class="grid grid-cols-2 gap-6 pt-4">
                <div class="space-y-2">
                    <h4 class="font-extrabold text-3xl text-brand-500">08:00 AM</h4>
                    <p class="text-sm text-slate-500">Jam Buka Pagi</p>
                </div>
                <div class="space-y-2">
                    <h4 class="font-extrabold text-3xl text-brand-500">11:00 PM</h4>
                    <p class="text-sm text-slate-500">Jam Tutup Malam</p>
                </div>
            </div>
        </div>

        <div class="relative">
            <img src="https://images.unsplash.com/photo-1445116572660-236099ec97a0?w=800&auto=format&fit=crop&q=80" alt="Cafe Latte Art" class="rounded-[2.5rem] w-full object-cover aspect-[4/3] shadow-xl border border-brand-500/10">
            <div class="absolute -bottom-6 -left-6 w-1/2 hidden md:block">
                <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=400&auto=format&fit=crop&q=80" alt="Cozy Workspace" class="rounded-3xl w-full object-cover aspect-square shadow-2xl border-4 border-[#faf9f6] dark:border-[#09090d]">
            </div>
        </div>
    </section>

    <!-- Menu Catalog Section -->
    <section id="menu-section" class="scroll-mt-20 space-y-8">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-800 dark:text-white">Menu Pilihan Kami</h2>
            <p class="text-slate-500 dark:text-slate-400">
                Jelajahi ragam cita rasa kopi autentik, minuman segar, camilan lezat, hingga paket hemat spesial dari Ngafe yang selalu terhubung langsung dengan ketersediaan di kasir kami.
            </p>
        </div>

        <!-- Category Tab Buttons -->
        <div class="flex flex-wrap justify-center gap-2 md:gap-3">
            <button onclick="filterCategory('all')" 
                id="tab-all"
                class="category-tab px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 bg-brand-500 text-white shadow-md shadow-brand-500/20">
                Semua Menu
            </button>
            @foreach($categories as $category)
                <button onclick="filterCategory('{{ Str::slug($category->name) }}')" 
                    id="tab-{{ Str::slug($category->name) }}"
                    class="category-tab px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-brand-50 dark:hover:bg-slate-700/50 border border-slate-200 dark:border-slate-700">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        <!-- Menu Items Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="products-grid">
            @php $hasAvailableProducts = false; @endphp
            @foreach($categories as $category)
                @foreach($category->products as $product)
                    @php 
                        $hasAvailableProducts = true; 
                        $imageSrc = $product->image ? asset('storage/' . $product->image) : ($categoryImages[$category->name] ?? $categoryImages['default']);
                    @endphp
                    <!-- Product Card -->
                    <div class="product-card glass-luxury rounded-3xl overflow-hidden border border-brand-500/10 dark:border-brand-500/5 group hover:-translate-y-1.5 hover:shadow-xl hover:shadow-brand-500/5 transition-all duration-300 flex flex-col h-full" 
                        data-category="{{ Str::slug($category->name) }}">
                        
                        <!-- Image Container -->
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="{{ $imageSrc }}" 
                                alt="{{ $product->name }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-black/60 text-white backdrop-blur-md">
                                    {{ $category->name }}
                                </span>
                                @if($product->is_package)
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-brand-500 text-white">
                                        <i class="fa-solid fa-gift mr-1"></i> Paket Hemat
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex flex-col flex-grow justify-between gap-4">
                            <div class="space-y-2">
                                <h3 class="font-bold text-lg text-slate-800 dark:text-white line-clamp-1 group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i> Tersedia untuk Dine-in & Takeaway
                                </p>
                            </div>
                            
                            <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-4">
                                <div class="space-y-0.5">
                                    <span class="text-xs text-slate-400">Harga</span>
                                    <p class="font-extrabold text-brand-600 dark:text-brand-400 text-lg">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <a href="{{ route('login') }}" class="w-10 h-10 rounded-2xl bg-brand-500/10 text-brand-500 hover:bg-brand-500 hover:text-white flex items-center justify-center transition-all duration-300">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach

            @if(!$hasAvailableProducts)
                <div class="col-span-full py-16 text-center text-slate-400 dark:text-slate-500 space-y-4">
                    <i class="fa-solid fa-box-open text-6xl text-brand-500/20"></i>
                    <p class="text-lg">Belum ada menu yang tersedia saat ini.</p>
                </div>
            @endif
        </div>

        <!-- Show More/Less Button -->
        <div class="flex justify-center pt-4" id="toggle-menu-container" style="display: none;">
            <button onclick="toggleMenuLimit()" 
                id="toggle-menu-btn"
                class="px-8 py-3 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-2xl shadow-lg shadow-brand-500/20 transition-all duration-300 transform hover:-translate-y-0.5 flex items-center gap-2">
                <span>Lihat Selengkapnya</span>
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" id="toggle-btn-icon"></i>
            </button>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery-section" class="scroll-mt-20 space-y-8">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-500 text-sm font-semibold tracking-wide">
                Galeri Cafe
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-800 dark:text-white">Sudut Estetik Ngafe</h2>
            <p class="text-slate-500 dark:text-slate-400">
                Tempat terbaik untuk mengabadikan momen berharga Anda dengan interior berkelas dan sajian berjiwa seni.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="overflow-hidden rounded-3xl aspect-[3/4] shadow-md border border-brand-500/10 group">
                <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=500&auto=format&fit=crop&q=80" alt="Workspace Cozy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            </div>
            <div class="overflow-hidden rounded-3xl aspect-[3/4] shadow-md border border-brand-500/10 group md:translate-y-6 transition-transform">
                <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=500&auto=format&fit=crop&q=80" alt="Cafe Barista Area" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            </div>
            <div class="overflow-hidden rounded-3xl aspect-[3/4] shadow-md border border-brand-500/10 group">
                <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500&auto=format&fit=crop&q=80" alt="Coffee Brewing" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            </div>
            <div class="overflow-hidden rounded-3xl aspect-[3/4] shadow-md border border-brand-500/10 group md:translate-y-6 transition-transform">
                <img src="https://images.unsplash.com/photo-1579888074068-19c3a9e4fde3?w=500&auto=format&fit=crop&q=80" alt="Fresh Bakeries" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section id="testimonial-section" class="scroll-mt-20 space-y-8">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-500 text-sm font-semibold tracking-wide">
                Testimoni
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-800 dark:text-white">Apa Kata Pengunjung Kami?</h2>
            <p class="text-slate-500 dark:text-slate-400">
                Kepuasan dan senyum Anda adalah prioritas utama dari pelayanan penuh cinta di Ngafe.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="glass-luxury p-8 rounded-3xl border border-brand-500/10 flex flex-col justify-between gap-6 relative">
                <i class="fa-solid fa-quote-left text-brand-500/10 text-6xl absolute top-6 right-6"></i>
                <p class="text-slate-600 dark:text-slate-300 italic text-sm leading-relaxed z-10">
                    "Kopi terbaik di daerah Senopati. Suasananya sangat cocok untuk remote working. Wi-Fi kencang dan pastry-nya selalu hangat dan segar dari oven!"
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-500 text-white flex items-center justify-center font-bold text-sm">
                        SK
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-800 dark:text-white">Sarah Kartika</h4>
                        <p class="text-[10px] text-slate-400">Software Engineer</p>
                    </div>
                </div>
            </div>

            <div class="glass-luxury p-8 rounded-3xl border border-brand-500/10 flex flex-col justify-between gap-6 relative">
                <i class="fa-solid fa-quote-left text-brand-500/10 text-6xl absolute top-6 right-6"></i>
                <p class="text-slate-600 dark:text-slate-300 italic text-sm leading-relaxed z-10">
                    "Tempat paling pas untuk meeting dengan klien atau sekedar diskusi santai. Pelayanan ramah, barista profesional, dan steak beef-nya bener-bener juara."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-500 text-white flex items-center justify-center font-bold text-sm">
                        BH
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-800 dark:text-white">Budi Hartono</h4>
                        <p class="text-[10px] text-slate-400">Entrepreneur</p>
                    </div>
                </div>
            </div>

            <div class="glass-luxury p-8 rounded-3xl border border-brand-500/10 flex flex-col justify-between gap-6 relative">
                <i class="fa-solid fa-quote-left text-brand-500/10 text-6xl absolute top-6 right-6"></i>
                <p class="text-slate-600 dark:text-slate-300 italic text-sm leading-relaxed z-10">
                    "Suka banget sama Americano Velvet & Croissant Almond-nya! Harganya juga sangat bersahabat dibanding cafe lain di sekitarnya. Tempatnya instagramable banget."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-500 text-white flex items-center justify-center font-bold text-sm">
                        AR
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-800 dark:text-white">Amanda Rosiana</h4>
                        <p class="text-[10px] text-slate-400">Content Creator</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Operational & Contact Info -->
    <section id="contact-section" class="scroll-mt-20 glass-luxury rounded-[2.5rem] p-8 md:p-16 border border-brand-500/10 dark:border-brand-500/5 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="space-y-6">
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Kunjungi Kami</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                Kami berlokasi strategis di pusat kota dengan akses mudah dan parkir luas. Kami menunggu kedatangan Anda!
            </p>
            <div class="space-y-4 text-sm">
                <div class="flex items-start gap-3 text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-location-dot text-brand-500 text-lg mt-0.5"></i>
                    <span>Jl. Kopi Harapan No. 42, Senopati, Jakarta Selatan</span>
                </div>
                <div class="flex items-center gap-3 text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-phone text-brand-500"></i>
                    <span>+62 812-3456-7890</span>
                </div>
                <div class="flex items-center gap-3 text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-envelope text-brand-500"></i>
                    <span>hello@ngafe.com</span>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Jam Operasional</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <span class="text-slate-500 dark:text-slate-400">Senin - Jumat</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">08:00 AM - 10:00 PM</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <span class="text-slate-500 dark:text-slate-400">Sabtu</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">08:00 AM - 11:00 PM</span>
                </div>
                <div class="flex justify-between pb-2">
                    <span class="text-slate-500 dark:text-slate-400">Minggu</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">08:00 AM - 11:00 PM</span>
                </div>
            </div>
            <div class="p-4 rounded-2xl bg-brand-500/10 border border-brand-500/20 text-brand-600 dark:text-brand-400 text-xs flex gap-2.5">
                <i class="fa-solid fa-circle-info text-base mt-0.5"></i>
                <p>Jam operasional dapat berubah pada hari libur nasional. Ikuti Instagram kami untuk update terbaru.</p>
            </div>
        </div>

        <div class="space-y-6">
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Ikuti Sosial Media</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                Tetap terhubung untuk mendapatkan promo menarik, menu baru, dan event komunitas seru di cafe kami.
            </p>
            <div class="flex gap-4">
                <a href="#" class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 hover:bg-brand-500 hover:text-white text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-lg transition-all duration-300">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 hover:bg-brand-500 hover:text-white text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-lg transition-all duration-300">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 hover:bg-brand-500 hover:text-white text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-lg transition-all duration-300">
                    <i class="fa-brands fa-tiktok"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 hover:bg-brand-500 hover:text-white text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-lg transition-all duration-300">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq-section" class="scroll-mt-20 max-w-4xl mx-auto space-y-8">
        <div class="text-center space-y-3">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-500 text-sm font-semibold tracking-wide">
                FAQ
            </div>
            <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white">Pertanyaan Sering Diajukan</h2>
            <p class="text-slate-500 dark:text-slate-400">
                Temukan jawaban cepat mengenai informasi fasilitas, layanan, dan sajian di kafe kami.
            </p>
        </div>

        <div class="space-y-4">
            <details class="group class-luxury border border-brand-500/10 rounded-2xl p-6 [&_summary::-webkit-details-marker]:hidden transition-all duration-300">
                <summary class="flex justify-between items-center font-bold text-slate-800 dark:text-white cursor-pointer select-none">
                    <span>Apakah Ngafe menyediakan area outdoor / smoking room?</span>
                    <span class="transition group-open:rotate-180 text-brand-500">
                        <i class="fa-solid fa-chevron-down"></i>
                    </span>
                </summary>
                <p class="mt-4 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    Ya, kami menyediakan area outdoor semi-terbuka yang asri di lantai dua dan teras depan untuk merokok dan bersantai di luar ruangan.
                </p>
            </details>

            <details class="group class-luxury border border-brand-500/10 rounded-2xl p-6 [&_summary::-webkit-details-marker]:hidden transition-all duration-300">
                <summary class="flex justify-between items-center font-bold text-slate-800 dark:text-white cursor-pointer select-none">
                    <span>Apakah bisa melakukan reservasi tempat untuk acara khusus?</span>
                    <span class="transition group-open:rotate-180 text-brand-500">
                        <i class="fa-solid fa-chevron-down"></i>
                    </span>
                </summary>
                <p class="mt-4 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    Tentu saja! Kami menerima reservasi tempat untuk rapat, ulang tahun, acara komunitas, atau perayaan spesial lainnya. Silakan hubungi admin kami melalui tombol WhatsApp/Telepon untuk info harga dan ketersediaan slot.
                </p>
            </details>

            <details class="group class-luxury border border-brand-500/10 rounded-2xl p-6 [&_summary::-webkit-details-marker]:hidden transition-all duration-300">
                <summary class="flex justify-between items-center font-bold text-slate-800 dark:text-white cursor-pointer select-none">
                    <span>Apakah semua menu makanan dan minuman halal?</span>
                    <span class="transition group-open:rotate-180 text-brand-500">
                        <i class="fa-solid fa-chevron-down"></i>
                    </span>
                </summary>
                <p class="mt-4 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    Ya, seluruh makanan dan minuman yang kami sajikan di Ngafe dijamin menggunakan bahan-bahan 100% halal serta diproses dengan standar kebersihan tertinggi.
                </p>
            </details>

            <details class="group class-luxury border border-brand-500/10 rounded-2xl p-6 [&_summary::-webkit-details-marker]:hidden transition-all duration-300">
                <summary class="flex justify-between items-center font-bold text-slate-800 dark:text-white cursor-pointer select-none">
                    <span>Apakah tersedia fasilitas parkir yang aman?</span>
                    <span class="transition group-open:rotate-180 text-brand-500">
                        <i class="fa-solid fa-chevron-down"></i>
                    </span>
                </summary>
                <p class="mt-4 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    Kami menyediakan fasilitas parkir mobil dan motor yang aman dan diawasi petugas keamanan serta CCTV 24 jam. Layanan parkir valet juga tersedia saat akhir pekan.
                </p>
            </details>
        </div>
    </section>
</div>

<!-- Category Filtering & Limit Script -->
<script>
let currentCategory = 'all';
let isExpanded = false;
const DEFAULT_LIMIT = 8;

function filterCategory(categorySlug) {
    currentCategory = categorySlug;
    isExpanded = false; // Reset to collapsed state on category change
    
    // Update active tab styles
    document.querySelectorAll('.category-tab').forEach(btn => {
        btn.classList.remove('bg-brand-500', 'text-white', 'shadow-md', 'shadow-brand-500/20');
        btn.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300', 'border', 'border-slate-200', 'dark:border-slate-700');
    });

    const activeBtn = document.getElementById('tab-' + categorySlug);
    if (activeBtn) {
        activeBtn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300', 'border', 'border-slate-200', 'dark:border-slate-700');
        activeBtn.classList.add('bg-brand-500', 'text-white', 'shadow-md', 'shadow-brand-500/20');
    }

    renderProducts();
}

function renderProducts() {
    let visibleCount = 0;
    let totalMatching = 0;
    
    const cards = document.querySelectorAll('.product-card');
    cards.forEach(card => {
        const matchesCategory = (currentCategory === 'all' || card.getAttribute('data-category') === currentCategory);
        
        if (matchesCategory) {
            totalMatching++;
            if (isExpanded || visibleCount < DEFAULT_LIMIT) {
                card.style.display = 'flex';
                card.classList.add('animate-fade-in-up');
                visibleCount++;
            } else {
                card.style.display = 'none';
                card.classList.remove('animate-fade-in-up');
            }
        } else {
            card.style.display = 'none';
            card.classList.remove('animate-fade-in-up');
        }
    });

    // Update the Show More button visibility, text, and icon rotation
    const container = document.getElementById('toggle-menu-container');
    const buttonSpan = document.querySelector('#toggle-menu-btn span');
    const icon = document.getElementById('toggle-btn-icon');
    
    if (totalMatching > DEFAULT_LIMIT) {
        container.style.display = 'flex';
        if (isExpanded) {
            buttonSpan.textContent = 'Tampilkan Lebih Sedikit';
            icon.classList.add('rotate-180');
        } else {
            buttonSpan.textContent = 'Lihat Selengkapnya';
            icon.classList.remove('rotate-180');
        }
    } else {
        container.style.display = 'none';
    }
}

function toggleMenuLimit() {
    isExpanded = !isExpanded;
    renderProducts();
}

// Initial render once DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    renderProducts();
});
</script>
@endsection