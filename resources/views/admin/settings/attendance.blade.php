@extends('admin.layout')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 bg-emerald-500/20 text-emerald-500 rounded-xl flex items-center justify-center text-2xl">
            <i class="fa-solid fa-map-location-dot"></i>
        </div>
        <div>
            <h2 class="text-3xl font-bold">Pengaturan Lokasi Absensi</h2>
            <p class="text-slate-400">Atur pusat koordinat kafe dan batas radius absen pegawai.</p>
        </div>
    </div>

    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-xl mb-6">
        <form action="{{ route('admin.settings.attendance.update') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-400 mb-2">Latitude (Garis Lintang)</label>
                <input type="text" name="attendance_lat" value="{{ $lat->value }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-emerald-500 focus:ring focus:ring-emerald-500/20 outline-none transition" required placeholder="Contoh: -6.200000">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-400 mb-2">Longitude (Garis Bujur)</label>
                <input type="text" name="attendance_lng" value="{{ $lng->value }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-emerald-500 focus:ring focus:ring-emerald-500/20 outline-none transition" required placeholder="Contoh: 106.816666">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-400 mb-2">Radius Maksimal (Meter)</label>
                <input type="number" name="attendance_radius" value="{{ $radius->value }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-emerald-500 focus:ring focus:ring-emerald-500/20 outline-none transition" required min="10">
                <p class="text-xs text-slate-500 mt-2"><i class="fa-solid fa-info-circle"></i> Jarak maksimal yang diizinkan untuk pegawai melakukan absen (Standar: 50 meter).</p>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl shadow-lg transition duration-300 flex items-center justify-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Pengaturan
            </button>
        </form>
    </div>
</div>
@endsection
