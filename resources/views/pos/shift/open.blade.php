@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto mt-12 bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-700">
    <div class="text-center mb-8">
        <div class="inline-block p-4 rounded-full bg-emerald-500/20 text-emerald-500 mb-4">
            <i class="fa-solid fa-cash-register text-4xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-white">Buka Shift Kasir</h2>
        <p class="text-slate-400 text-sm mt-2">Jadwal Anda hari ini: <strong class="text-emerald-400">{{ $schedule->shift->name }} ({{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }})</strong></p>
        <p class="text-slate-400 text-sm mt-2">Anda harus memasukkan modal awal laci sebelum memulai pesanan.</p>
    </div>
    
    <form action="{{ route('pos.shift.storeOpen') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">Modal Awal / Uang Kembalian (Rp)</label>
            <input type="number" name="opening_cash" required min="0" placeholder="Contoh: 100000" class="w-full bg-slate-900 border border-slate-600 rounded-xl p-4 text-white text-lg focus:border-emerald-500 focus:ring focus:ring-emerald-500/20 outline-none transition">
        </div>
        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-4 rounded-xl shadow-lg transition duration-300">
            <i class="fa-solid fa-lock-open mr-2"></i> Buka Kasir Sekarang
        </button>
    </form>
</div>
@endsection