@extends('admin.layout')
@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header Section -->
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-500/20 text-red-400 border border-red-500/30">
            <i class="fa-solid fa-receipt text-base"></i>
        </span>
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Konfigurasi Pajak & Layanan</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Atur pengenaan PPN dan biaya layanan (Service Charge) pada struk kasir.</p>
        </div>
    </div>

    <form action="{{ route('admin.cafe.tax.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Pajak / PPN Card --}}
        <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700/60 mb-5">
                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Pajak Pertambahan Nilai (PPN)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Dikenakan secara otomatis pada kalkulasi transaksi pesanan.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="tax_enabled" id="taxEnabled" value="1" class="sr-only peer" {{ $taxEnabled ? 'checked' : '' }}>
                    <div class="w-12 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-red-500 rounded-full peer transition-colors"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-6"></div>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Label Struk Pajak</label>
                    <input type="text" name="tax_label" value="{{ $taxLabel }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-red-500 outline-none transition" placeholder="PPN 11%">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Tarif Pajak (%)</label>
                    <input type="number" name="tax_rate" value="{{ $taxRate }}" step="0.01" min="0" max="100" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-red-500 outline-none transition" placeholder="11">
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                <div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Pajak Inklusif (Harga Sudah Termasuk Pajak)</span>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jika aktif, total tagihan tidak ditambah lagi tetapi struk memisahkan komponen PPN.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                    <input type="checkbox" name="tax_inclusive" value="1" class="sr-only peer" {{ $taxInclusive ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-red-500 rounded-full peer transition-colors"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </label>
            </div>
        </div>

        {{-- Service Charge Card --}}
        <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700/60 mb-5">
                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Biaya Layanan (Service Charge)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Biaya tambahan atas operasional pelayanan restoran/kafe.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="service_charge_enabled" value="1" class="sr-only peer" {{ $serviceCharge ? 'checked' : '' }}>
                    <div class="w-12 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-amber-500 rounded-full peer transition-colors"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-6"></div>
                </label>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Tarif Biaya Layanan (%)</label>
                <input type="number" name="service_charge_rate" value="{{ $serviceRate }}" step="0.01" min="0" max="100" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-amber-500 outline-none transition" placeholder="5">
            </div>
        </div>

        {{-- Preview Box --}}
        <div class="bg-slate-50 dark:bg-slate-900/90 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 text-xs sm:text-sm">
            <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white mb-3">
                <i class="fa-solid fa-calculator text-brand-500"></i>
                <span>Simulasi Perhitungan Tagihan (Acuan Subtotal Rp 100.000)</span>
            </div>
            <div class="space-y-2 text-slate-600 dark:text-slate-400">
                <div class="flex justify-between">
                    <span>Subtotal Produk</span>
                    <span class="font-mono font-semibold text-slate-900 dark:text-white">Rp 100.000</span>
                </div>
                <div class="flex justify-between">
                    <span>Service Charge ({{ $serviceRate }}%)</span>
                    <span class="font-mono font-semibold text-amber-600 dark:text-amber-400">+ Rp {{ number_format($serviceRate * 1000, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ $taxLabel }} ({{ $taxRate }}%)</span>
                    <span class="font-mono font-semibold text-red-600 dark:text-red-400">+ Rp {{ number_format(($taxRate/100) * 100000, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 dark:border-slate-700 pt-2 font-bold text-slate-900 dark:text-white text-sm">
                    <span>Total Transaksi</span>
                    <span class="font-mono text-emerald-600 dark:text-emerald-400 text-base">
                        Rp {{ number_format(100000 + ($serviceRate * 1000) + (($taxRate/100) * 100000), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-red-600/20 active:scale-95 transition text-sm">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Konfigurasi Pajak</span>
            </button>
        </div>
    </form>
</div>
@endsection
