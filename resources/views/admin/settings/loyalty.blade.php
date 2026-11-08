@extends('admin.layout')
@section('content')
<div class="space-y-6 max-w-2xl mx-auto">
    <!-- Header Section -->
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-brand-500/20 text-brand-500 border border-brand-500/30">
            <i class="fa-solid fa-coins text-base"></i>
        </span>
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Aturan Loyalitas Poin</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Konfigurasi nilai perolehan poin per nominal belanja serta nilai tukar potongan harga.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 p-4 sm:p-7 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <form action="{{ route('admin.settings.loyalty.update') }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- Earn Points Box --}}
            <div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-950 border border-brand-500/30 rounded-2xl space-y-3">
                <h3 class="font-bold text-sm text-brand-600 dark:text-brand-400 flex items-center gap-2">
                    <i class="fa-solid fa-gift"></i>
                    <span>Aturan Perolehan Poin Belanja</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Setiap pelanggan belanja kelipatan nominal di bawah ini, mereka otomatis memperoleh <b>1 Poin</b>.</p>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Batas Belanja per 1 Poin</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="point_earn_threshold" value="{{ $earn_rate->value ?? 10000 }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 dark:text-white text-sm font-mono focus:border-brand-500 outline-none transition" required>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Contoh: Rp 10.000. Belanja Rp 50.000 akan mendapatkan 5 Poin.</p>
                </div>
            </div>
            
            {{-- Redeem Points Box --}}
            <div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-950 border border-emerald-500/30 rounded-2xl space-y-3">
                <h3 class="font-bold text-sm text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                    <i class="fa-solid fa-ticket"></i>
                    <span>Aturan Penukaran Poin Diskon</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Nilai rupiah potongan tagihan untuk setiap <b>1 Poin</b> yang ditukarkan di kasir.</p>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nilai Diskon per 1 Poin</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="point_redeem_value" value="{{ $redeem_value->value ?? 100 }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 dark:text-white text-sm font-mono focus:border-emerald-500 outline-none transition" required>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Contoh: Rp 100. Jika member menukar 50 Poin, mendapatkan potongan tagihan Rp 5.000.</p>
                </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-500 hover:to-amber-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-brand-500/20 active:scale-95 transition flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Aturan Loyalitas Poin</span>
            </button>
        </form>
    </div>
</div>
@endsection