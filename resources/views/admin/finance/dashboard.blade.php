@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                <i class="fa-solid fa-chart-line text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Dashboard Keuangan</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Analisis ringkas omset penjualan, estimasi HPP, beban operasional, dan margin laba bersih.</p>
            </div>
        </div>
        
        <!-- Filter Form -->
        <form class="flex flex-wrap sm:flex-nowrap items-center gap-2 bg-white dark:bg-slate-800 p-2 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <select name="month" class="bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs sm:text-sm rounded-xl px-3 py-2 outline-none focus:border-brand-500">
                @for($i=1; $i<=12; $i++)
                    <option value="{{ sprintf('%02d', $i) }}" {{ $month == sprintf('%02d', $i) ? 'selected' : '' }}>
                        {{ date('F', mktime(0,0,0,$i,10)) }}
                    </option>
                @endfor
            </select>
            <select name="year" class="bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs sm:text-sm rounded-xl px-3 py-2 outline-none focus:border-brand-500">
                @for($i=date('Y')-2; $i<=date('Y'); $i++)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Filter</span>
            </button>
        </form>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border-t-4 border-blue-500 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">
                <span>Gross Revenue</span>
                <i class="fa-solid fa-arrow-trend-up text-blue-500 text-sm"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-blue-600 dark:text-blue-400 font-mono">
                Rp {{ number_format($grossRevenue, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Total kotor pesanan terbayar</div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border-t-4 border-orange-500 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">
                <span>Est. HPP (COGS)</span>
                <i class="fa-solid fa-boxes-stacked text-orange-500 text-sm"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-orange-600 dark:text-orange-400 font-mono">
                Rp {{ number_format($cogs, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Beban bahan baku terpakai</div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border-t-4 border-purple-500 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">
                <span>Beban Operasional</span>
                <i class="fa-solid fa-receipt text-purple-500 text-sm"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-purple-600 dark:text-purple-400 font-mono">
                Rp {{ number_format($expenses, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Gaji, utilitas, promosi, sewa</div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border-t-4 {{ $netProfit >= 0 ? 'border-emerald-500' : 'border-red-500' }} border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden">
            <i class="fa-solid fa-coins absolute -right-3 -bottom-3 text-7xl {{ $netProfit >= 0 ? 'text-emerald-500/10' : 'text-red-500/10' }} pointer-events-none"></i>
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-2 relative z-10">
                <span>Net Profit (Bersih)</span>
                <i class="fa-solid fa-wallet {{ $netProfit >= 0 ? 'text-emerald-500' : 'text-red-500' }} text-sm"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black {{ $netProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} font-mono relative z-10">
                Rp {{ number_format($netProfit, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1 relative z-10">Laba bersih setelah semua biaya</div>
        </div>
    </div>

    <!-- Margin & Best Sellers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Laba Kotor Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-brand-500/15 text-brand-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-chart-pie"></i>
                    </span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Laba Kotor (Gross Margin)</h3>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white font-mono mb-2">
                    Rp {{ number_format($grossProfit, 0, ',', '.') }}
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Pendapatan omset kotor dikurangi taksiran modal bahan baku. Merupakan margin utama produk sebelum dipotong biaya operasional bulanan seperti gaji dan utilitas.
                </p>
            </div>
            
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/60">
                <div class="flex justify-between text-xs font-semibold mb-2">
                    <span class="text-slate-600 dark:text-slate-400">Margin Persentase Penjualan</span>
                    <span class="text-brand-600 dark:text-brand-400 font-bold">
                        {{ $grossRevenue > 0 ? round(($grossProfit / $grossRevenue) * 100, 1) : 0 }}%
                    </span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700/60 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-brand-600 to-amber-500 h-3 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $grossRevenue > 0 ? round(($grossProfit / $grossRevenue) * 100, 1) : 0)) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Best Sellers Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-orange-500/15 text-orange-500 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-fire"></i>
                    </span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Menu Terlaris (Best Sellers)</h3>
                </div>
                <span class="text-xs text-slate-400">Periode Bulan Ini</span>
            </div>
            
            <div class="space-y-3">
                @forelse($bestSellers as $item)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700/40">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold {{ $loop->first ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                            {{ $loop->iteration }}
                        </div>
                        <div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white">{{ $item->product->name ?? 'Produk' }}</div>
                            <div class="text-[11px] text-slate-400">{{ $item->total_qty }} porsi terjual</div>
                        </div>
                    </div>
                    <div class="font-bold font-mono text-emerald-600 dark:text-emerald-400 text-sm">
                        Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div class="text-center text-slate-400 py-8 text-xs">
                    <i class="fa-solid fa-mug-hot text-2xl mb-2 opacity-30"></i>
                    <p>Belum ada rekapan penjualan di bulan ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection