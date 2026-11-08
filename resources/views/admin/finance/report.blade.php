@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-500/30">
                <i class="fa-solid fa-file-invoice-dollar text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Laporan Laba Rugi</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Rekapitulasi keuangan komprehensif untuk pemilik bisnis & investor.</p>
            </div>
        </div>
        
        <!-- Filter Form & Print -->
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
                <span>Cetak PDF</span>
            </button>
        </form>
    </div>

    <!-- Income Statement Sheet -->
    <div class="bg-white text-slate-900 p-6 sm:p-10 md:p-12 rounded-3xl shadow-xl max-w-4xl mx-auto border border-slate-200" id="printableArea">
        <div class="text-center mb-8 pb-6 border-b-2 border-slate-800">
            <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Ngafe Coffee & Space</span>
            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-wider text-slate-950 mt-1">Laporan Laba Rugi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Periode Akuntansi: {{ date('F Y', mktime(0,0,0,$month,10,$year)) }}</p>
        </div>

        <div class="space-y-6 text-sm">
            <!-- Pendapatan -->
            <div>
                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 pb-2 border-b border-slate-200">1. Pendapatan Usaha (Revenue)</h3>
                <div class="flex justify-between items-center py-2.5 px-2">
                    <span class="text-slate-700">Total Penjualan Kotor (Gross Sales)</span>
                    <span class="font-mono font-semibold">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2.5 px-3 bg-slate-50 rounded-xl font-bold mt-1 border border-slate-200 text-slate-900">
                    <span>Total Pendapatan Bersih</span>
                    <span class="font-mono text-base">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- HPP -->
            <div>
                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 pb-2 border-b border-slate-200">2. Harga Pokok Penjualan (COGS)</h3>
                <div class="flex justify-between items-center py-2.5 px-2">
                    <span class="text-slate-700">Beban Pemakaian Bahan Baku (Estimasi)</span>
                    <span class="font-mono font-semibold text-red-600">(Rp {{ number_format($cogs, 0, ',', '.') }})</span>
                </div>
                <div class="flex justify-between items-center py-2.5 px-3 bg-blue-50/60 rounded-xl font-bold mt-1 border border-blue-200 text-blue-900">
                    <span>Laba Kotor (Gross Profit)</span>
                    <span class="font-mono text-base">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- OPEX -->
            <div>
                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 pb-2 border-b border-slate-200">3. Beban Operasional (OPEX)</h3>
                <div class="space-y-1 mt-1">
                    @forelse($expenseCategories as $cat)
                    <div class="flex justify-between items-center py-2 px-2 border-b border-slate-100 last:border-0 text-slate-700">
                        <span>Beban {{ $cat->category }}</span>
                        <span class="font-mono font-medium">(Rp {{ number_format($cat->total, 0, ',', '.') }})</span>
                    </div>
                    @empty
                    <div class="py-2 px-2 text-slate-400 italic text-xs">Belum ada rincian pengeluaran di periode ini.</div>
                    @endforelse
                </div>
                <div class="flex justify-between items-center py-2.5 px-3 bg-red-50/60 rounded-xl font-bold mt-2 border border-red-200 text-red-900">
                    <span>Total Beban Operasional</span>
                    <span class="font-mono text-base">(Rp {{ number_format($totalExpenses, 0, ',', '.') }})</span>
                </div>
            </div>

            <!-- Laba Bersih -->
            <div class="pt-4 border-t-2 border-slate-800">
                <div class="flex items-center justify-between p-4 sm:p-5 bg-slate-900 text-white rounded-2xl shadow-md">
                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 block font-bold">Hasil Akhir Periode</span>
                        <span class="text-base sm:text-lg font-black uppercase">Laba Bersih (Net Profit)</span>
                    </div>
                    <span class="font-mono text-xl sm:text-2xl font-black {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Signatures -->
        <div class="mt-12 pt-8 border-t border-slate-200 grid grid-cols-2 text-center text-xs text-slate-700">
            <div class="w-40 mx-auto">
                <p class="mb-14">Disetujui Oleh,</p>
                <div class="border-t border-slate-800 pt-2 font-bold text-slate-900">Owner / Direktur</div>
            </div>
            <div class="w-40 mx-auto">
                <p class="mb-14">Dibuat Oleh,</p>
                <div class="border-t border-slate-800 pt-2 font-bold text-slate-900">Finance & Kasir</div>
            </div>
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
        padding: 0 !important;
    }
}
</style>
@endsection