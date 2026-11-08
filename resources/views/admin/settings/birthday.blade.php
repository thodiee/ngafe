@extends('admin.layout')
@section('content')
<div class="space-y-6 max-w-2xl mx-auto">
    <!-- Header Section -->
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-pink-500/20 text-pink-400 border border-pink-500/30">
            <i class="fa-solid fa-cake-candles text-base"></i>
        </span>
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Promo Ulang Tahun Member</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Atur pemberian hadiah spesial atau diskon otomatis untuk member yang berulang tahun.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 p-4 sm:p-7 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <form action="{{ route('admin.settings.birthday.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-950 border border-amber-500/30 rounded-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-sm text-amber-600 dark:text-amber-400 flex items-center gap-2">
                        <i class="fa-solid fa-gift"></i>
                        <span>Status Promo Ulang Tahun</span>
                    </h3>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="radio" name="bday_promo_status" value="1" {{ $bday_promo_status->value == '1' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <span class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200">Aktif (Tampilkan Alert di POS)</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="radio" name="bday_promo_status" value="0" {{ $bday_promo_status->value == '0' ? 'checked' : '' }} class="text-slate-400 focus:ring-slate-400">
                        <span class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Nonaktifkan Promo</span>
                    </label>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kasir akan otomatis menerima pop-up notifikasi ucapan selamat & reminder hadiah saat member ulang tahun bertransaksi.</p>
            </div>
            
            <div class="p-5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-message text-brand-500"></i>
                    <span>Pesan Hadiah / Promo Kasir</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tuliskan petunjuk hadiah yang harus diberikan kasir ke member (misal: "Gratis 1 Kopi Susu Aren" atau "Diskon 30% All Beverage").</p>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Teks Instruksi Promo</label>
                    <input type="text" name="bday_promo_message" value="{{ old('bday_promo_message', $bday_promo_message->value) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-amber-500 outline-none transition" placeholder="Contoh: Berikan Gratis 1 Cup Brown Sugar Latte!" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-amber-600 to-brand-600 hover:from-amber-500 hover:to-brand-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-amber-600/20 active:scale-95 transition flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Pengaturan Promo</span>
            </button>
        </form>
    </div>
</div>
@endsection
