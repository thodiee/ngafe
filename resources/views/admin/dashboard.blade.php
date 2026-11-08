@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Welcome Header Banner -->
    <div class="bg-gradient-to-r from-amber-600 via-brand-500 to-amber-700 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-6 -bottom-8 opacity-10 text-9xl">
            <i class="fa-solid fa-mug-hot"></i>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 backdrop-blur-sm text-white mb-2 border border-white/20">
                    <i class="fa-solid fa-circle-check text-[10px]"></i> Ngafe Management System
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h1>
                <p class="text-white/80 text-sm mt-1 max-w-xl">Semua operasional kafe terkoneksi real-time: POS Kasir, Dapur KDS, Pelayan, Stok Bahan, dan Rekapitulasi Keuangan.</p>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-center">
                <a href="{{ route('pos.menu') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-slate-900 font-bold text-sm shadow-md hover:bg-slate-100 transition">
                    <i class="fa-solid fa-cash-register text-amber-600"></i> Buka Kasir POS
                </a>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Pegawai -->
        <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Karyawan</span>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-lg border border-blue-500/20">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="text-3xl font-black font-mono text-slate-900 dark:text-white">{{ $userCount }}</div>
            <p class="text-xs text-slate-400 mt-1">Staf & kru aktif di outlet</p>
        </div>

        <!-- Total Menu -->
        <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Menu & Produk</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg border border-emerald-500/20">
                    <i class="fa-solid fa-utensils"></i>
                </div>
            </div>
            <div class="text-3xl font-black font-mono text-slate-900 dark:text-white">{{ $productCount }}</div>
            <p class="text-xs text-slate-400 mt-1">Makanan, minuman & bundling</p>
        </div>

        <!-- Absensi Hari Ini -->
        <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Kehadiran Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-lg border border-purple-500/20">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
            </div>
            <div class="text-3xl font-black font-mono text-slate-900 dark:text-white">{{ $attendanceCount }}</div>
            <p class="text-xs text-slate-400 mt-1">Pegawai yang sudah clock-in</p>
        </div>

        <!-- Stok Tipis -->
        <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Bahan Baku Menipis</span>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-lg border border-rose-500/20">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-3xl font-black font-mono {{ $lowStock > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">{{ $lowStock }}</div>
            <p class="text-xs text-slate-400 mt-1">Bahan di bawah batas minimum</p>
        </div>
    </div>

    <!-- Quick Shortcuts Grid -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4">Akses Cepat Modul Utama</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('pos.menu') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 hover:bg-amber-500/10 dark:bg-slate-900/50 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700 transition group text-center">
                <i class="fa-solid fa-cash-register text-2xl text-amber-500 group-hover:scale-110 transition mb-2"></i>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Kasir POS</span>
            </a>
            <a href="{{ route('kitchen.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 hover:bg-orange-500/10 dark:bg-slate-900/50 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700 transition group text-center">
                <i class="fa-solid fa-fire-burner text-2xl text-orange-500 group-hover:scale-110 transition mb-2"></i>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Dapur KDS</span>
            </a>
            <a href="{{ route('waiter.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 hover:bg-yellow-500/10 dark:bg-slate-900/50 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700 transition group text-center">
                <i class="fa-solid fa-bell-concierge text-2xl text-yellow-500 group-hover:scale-110 transition mb-2"></i>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Waiter</span>
            </a>
            <a href="{{ route('admin.tables.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 hover:bg-blue-500/10 dark:bg-slate-900/50 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700 transition group text-center">
                <i class="fa-solid fa-border-all text-2xl text-blue-500 group-hover:scale-110 transition mb-2"></i>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Meja & QR</span>
            </a>
            <a href="{{ route('admin.packages.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 hover:bg-brand-500/10 dark:bg-slate-900/50 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700 transition group text-center">
                <i class="fa-solid fa-box-open text-2xl text-brand-500 group-hover:scale-110 transition mb-2"></i>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Paket Bundling</span>
            </a>
            <a href="{{ route('admin.finance.dashboard') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 hover:bg-cyan-500/10 dark:bg-slate-900/50 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700 transition group text-center">
                <i class="fa-solid fa-coins text-2xl text-cyan-500 group-hover:scale-110 transition mb-2"></i>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Keuangan</span>
            </a>
        </div>
    </div>

    <!-- Birthday Alert Notification -->
    @if($todayBirthdays && $todayBirthdays->count() > 0)
    <div class="bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-amber-500/10 border border-amber-500/30 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-cake-candles"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Pelanggan Ulang Tahun Hari Ini!</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Berikan ucapan selamat atau promo diskon ulang tahun spesial untuk menjaga loyalitas pelanggan</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($todayBirthdays as $bdayCust)
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-amber-500/30 shadow-sm flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-base">{{ $bdayCust->name }}</h4>
                    <p class="text-xs text-slate-500 font-mono mt-0.5"><i class="fa-solid fa-phone text-[10px] text-amber-500 mr-1"></i> {{ $bdayCust->phone }}</p>
                </div>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $bdayCust->phone) }}?text=Halo%20{{ urlencode($bdayCust->name) }},%20Selamat%20Ulang%20Tahun%20dari%20Ngafe!%20Nikmati%20promo%20spesial%20hari%20ini." target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-sm">
                    <i class="fa-brands fa-whatsapp text-sm"></i> Sapa WA
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection