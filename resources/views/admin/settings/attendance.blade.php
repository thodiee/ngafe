@extends('admin.layout')
@section('content')
<div class="space-y-6 max-w-2xl mx-auto">
    <!-- Header Section -->
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30">
            <i class="fa-solid fa-map-location-dot text-base"></i>
        </span>
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Pengaturan Lokasi GPS Absensi</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Tentukan titik pusat koordinat kafe dan toleransi radius kehadiran pegawai.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-6">
        <form action="{{ route('admin.settings.attendance.update') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700/60">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Titik Koordinat Kafe</span>
                <button type="button" onclick="detectCurrentLocation()" class="inline-flex items-center gap-1.5 text-xs text-brand-600 dark:text-brand-400 hover:underline font-semibold">
                    <i class="fa-solid fa-crosshairs"></i>
                    <span>Deteksi Lokasi Saya Sekarang</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Latitude (Lintang)</label>
                    <input type="text" name="attendance_lat" id="latInput" value="{{ $lat->value ?? '' }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm font-mono focus:border-brand-500 outline-none transition" required placeholder="Contoh: -6.200000">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Longitude (Bujur)</label>
                    <input type="text" name="attendance_lng" id="lngInput" value="{{ $lng->value ?? '' }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm font-mono focus:border-brand-500 outline-none transition" required placeholder="Contoh: 106.816666">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Radius Toleransi Maksimal (Meter)</label>
                <div class="relative">
                    <input type="number" name="attendance_radius" value="{{ $radius->value ?? 50 }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm font-mono focus:border-brand-500 outline-none transition" required min="10">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Meter</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-brand-500"></i>
                    <span>Jarak radius maksimal pegawai diperbolehkan clock-in (Rekomendasi standar: 50 - 100 meter).</span>
                </p>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-emerald-600/20 active:scale-95 transition flex items-center justify-center gap-2 text-sm">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Pengaturan Koordinat</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function detectCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                document.getElementById('latInput').value = pos.coords.latitude.toFixed(6);
                document.getElementById('lngInput').value = pos.coords.longitude.toFixed(6);
                alert('Lokasi GPS berhasil dideteksi: ' + pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6));
            },
            function(err) {
                alert('Gagal mendeteksi lokasi GPS: ' + err.message);
            }
        );
    } else {
        alert('Browser tidak mendukung pendeteksian lokasi GPS.');
    }
}
</script>
@endsection
