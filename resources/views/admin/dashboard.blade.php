@extends('admin.layout')
@section('content')
<h2 class="text-3xl font-bold mb-8 text-[#2c2520] dark:text-white">Admin Dashboard</h2>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border-t-4 border-blue-500 shadow-lg transition">
        <div class="text-slate-500 dark:text-slate-400 text-sm mb-2">Total Pegawai</div>
        <div class="text-3xl font-bold text-[#2c2520] dark:text-white">{{ $userCount }}</div>
    </div>
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border-t-4 border-emerald-500 shadow-lg transition">
        <div class="text-slate-500 dark:text-slate-400 text-sm mb-2">Total Menu</div>
        <div class="text-3xl font-bold text-[#2c2520] dark:text-white">{{ $productCount }}</div>
    </div>
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border-t-4 border-purple-500 shadow-lg transition">
        <div class="text-slate-500 dark:text-slate-400 text-sm mb-2">Absensi Hari Ini</div>
        <div class="text-3xl font-bold text-[#2c2520] dark:text-white">{{ $attendanceCount }}</div>
    </div>
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border-t-4 border-red-500 shadow-lg transition">
        <div class="text-slate-500 dark:text-slate-400 text-sm mb-2">Stok Tipis</div>
        <div class="text-3xl font-bold text-red-500 dark:text-red-400">{{ $lowStock }}</div>
    </div>
</div>

@if($todayBirthdays && $todayBirthdays->count() > 0)
<div class="mt-8">
    <h3 class="text-xl font-bold mb-4 text-amber-500 dark:text-amber-400"><i class="fa-solid fa-cake-candles mr-2"></i> Pelanggan Ulang Tahun Hari Ini</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($todayBirthdays as $bdayCust)
        <div class="bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-amber-500/50 p-4 rounded-xl shadow-lg relative overflow-hidden">
            <i class="fa-solid fa-gift absolute -right-4 -bottom-4 text-6xl text-amber-500/20"></i>
            <div class="font-bold text-lg text-[#2c2520] dark:text-white mb-1">{{ $bdayCust->name }}</div>
            <div class="text-sm text-amber-700 dark:text-amber-200"><i class="fa-solid fa-phone mr-1"></i> {{ $bdayCust->phone }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">Segera follow-up dan berikan promo spesial!</div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection