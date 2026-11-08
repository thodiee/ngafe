@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-500/30">
                <i class="fa-solid fa-chart-bar text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Laporan Penjualan (Sales Report)</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Analisis kuantitas produk terjual dan kontribusi omset per item menu.</p>
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
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Filter</span>
            </button>
            <button type="button" onclick="window.print()" class="bg-slate-800 hover:bg-slate-700 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-print text-xs"></i>
                <span>Cetak</span>
            </button>
        </form>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 print:hidden">
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border-t-4 border-blue-500 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Total Porsi Terjual</div>
            <div class="text-2xl sm:text-3xl font-black text-blue-600 dark:text-blue-400 font-mono">{{ $totalItems }} <span class="text-sm font-sans font-normal text-slate-400">Porsi</span></div>
            <div class="text-xs text-slate-400 mt-1">Akumulasi seluruh varian menu terjual</div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border-t-4 border-emerald-500 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Total Omset Penjualan</div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-400 mt-1">Bruto penjualan menu sebelum HPP</div>
        </div>
    </div>

    <!-- Printable Sales Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-xl" id="printableArea">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 hidden print:block text-center text-slate-900">
            <h1 class="text-2xl font-black uppercase tracking-wider">Laporan Penjualan Menu (Sales Report)</h1>
            <p class="text-sm mt-1">Periode: {{ date('F Y', mktime(0,0,0,$month,10,$year)) }} • Ngafe F&B Management</p>
        </div>

        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center print:hidden">
            <div>
                <h3 class="font-bold text-base text-slate-900 dark:text-white">Rincian Menu Terjual</h3>
                <p class="text-xs text-slate-400">Diurutkan berdasarkan kontribusi omset dan kuantitas</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                {{ count($productsSold) }} Menu Terjual
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Peringkat</th>
                        <th class="p-4 sm:px-6">Nama Produk Menu</th>
                        <th class="p-4 sm:px-6 text-center">Kategori</th>
                        <th class="p-4 sm:px-6 text-center">Qty Terjual</th>
                        <th class="p-4 sm:px-6 text-right">Total Omset (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($productsSold as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6">
                            <span class="w-7 h-7 rounded-lg inline-flex items-center justify-center font-bold text-xs {{ $loop->iteration <= 3 ? 'bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                                #{{ $loop->iteration }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6 font-bold text-slate-900 dark:text-white">
                            {{ $item->product->name ?? 'Unknown' }}
                        </td>
                        <td class="p-4 sm:px-6 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                {{ $item->product->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6 text-center font-mono font-bold text-blue-600 dark:text-blue-400">
                            {{ $item->total_qty }} porsi
                        </td>
                        <td class="p-4 sm:px-6 font-mono font-bold text-emerald-600 dark:text-emerald-400 text-right">
                            Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-chart-line text-4xl mb-3 opacity-30"></i>
                            <p>Tidak ada transaksi penjualan di bulan ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($productsSold->count() > 0)
                <tfoot class="bg-slate-50 dark:bg-slate-900 border-t-2 border-slate-200 dark:border-slate-700">
                    <tr>
                        <th colspan="3" class="p-4 sm:px-6 text-right uppercase text-xs font-bold text-slate-700 dark:text-slate-300 tracking-wider">
                            Total Penjualan Keseluruhan
                        </th>
                        <th class="p-4 sm:px-6 text-center font-mono font-bold text-blue-600 dark:text-blue-400 text-base">
                            {{ $totalItems }}
                        </th>
                        <th class="p-4 sm:px-6 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 text-base">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #printableArea, #printableArea * { visibility: visible; }
    #printableArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        box-shadow: none !important;
        border: none !important;
        background: white !important;
        color: #0f172a !important;
    }
}
</style>
@endsection