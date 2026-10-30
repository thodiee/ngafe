@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-white"><i class="fa-solid fa-file-invoice-dollar text-blue-400 mr-2"></i> Laporan Laba Rugi</h2>
    <form class="flex gap-2">
        <select name="month" class="bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2">
            @for($i=1; $i<=12; $i++)
                <option value="{{ sprintf('%02d', $i) }}" {{ $month == sprintf('%02d', $i) ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,10)) }}</option>
            @endfor
        </select>
        <select name="year" class="bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2">
            @for($i=date('Y')-2; $i<=date('Y'); $i++)
                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg transition"><i class="fa-solid fa-filter"></i> Filter</button>
        <button type="button" onclick="window.print()" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg transition ml-2"><i class="fa-solid fa-print"></i> Cetak</button>
    </form>
</div>

<div class="bg-white text-slate-900 p-8 md:p-12 rounded-xl shadow-2xl max-w-4xl mx-auto" id="printableArea">
    <div class="text-center mb-8 border-b-2 border-slate-900 pb-6">
        <h1 class="text-3xl font-bold uppercase tracking-widest">Laporan Laba Rugi</h1>
        <p class="text-lg mt-2 font-medium">Periode: {{ date('F Y', mktime(0,0,0,$month,10,$year)) }}</p>
    </div>

    <div class="space-y-6">
        <!-- Pendapatan -->
        <div>
            <h3 class="font-bold text-lg border-b border-slate-300 pb-1 mb-2 uppercase text-slate-700">Pendapatan (Revenue)</h3>
            <div class="flex justify-between items-center py-2 px-2">
                <span>Total Penjualan (Gross Revenue)</span>
                <span class="font-mono text-lg">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-2 px-2 bg-slate-100 font-bold mt-2 border-t border-slate-300">
                <span>Total Pendapatan</span>
                <span class="font-mono text-lg">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- HPP -->
        <div>
            <h3 class="font-bold text-lg border-b border-slate-300 pb-1 mb-2 uppercase text-slate-700">Harga Pokok Penjualan (COGS)</h3>
            <div class="flex justify-between items-center py-2 px-2">
                <span>Beban Bahan Baku (Estimasi)</span>
                <span class="font-mono text-lg">(Rp {{ number_format($cogs, 0, ',', '.') }})</span>
            </div>
            <div class="flex justify-between items-center py-2 px-2 bg-slate-100 font-bold mt-2 border-t border-slate-300 text-blue-800">
                <span>Laba Kotor (Gross Profit)</span>
                <span class="font-mono text-lg">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Beban Operasional -->
        <div>
            <h3 class="font-bold text-lg border-b border-slate-300 pb-1 mb-2 uppercase text-slate-700">Biaya Operasional (OPEX)</h3>
            @forelse($expenseCategories as $cat)
            <div class="flex justify-between items-center py-2 px-2 border-b border-slate-100 last:border-0">
                <span>Beban {{ $cat->category }}</span>
                <span class="font-mono text-lg">(Rp {{ number_format($cat->total, 0, ',', '.') }})</span>
            </div>
            @empty
            <div class="py-2 px-2 text-slate-500 italic">Tidak ada pengeluaran tercatat.</div>
            @endforelse
            <div class="flex justify-between items-center py-2 px-2 bg-slate-100 font-bold mt-2 border-t border-slate-300 text-red-800">
                <span>Total Biaya Operasional</span>
                <span class="font-mono text-lg">(Rp {{ number_format($totalExpenses, 0, ',', '.') }})</span>
            </div>
        </div>

        <!-- Laba Bersih -->
        <div class="mt-8 pt-4 border-t-4 border-slate-900">
            <div class="flex justify-between items-center py-4 px-2 bg-slate-900 text-white rounded-lg">
                <span class="font-bold text-xl uppercase tracking-wider ml-2">Laba Bersih (Net Profit)</span>
                <span class="font-mono text-2xl font-bold mr-2">Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    
    <div class="mt-16 pt-8 border-t border-slate-300 flex justify-between text-center text-sm">
        <div class="w-48">
            <p class="mb-16">Disetujui Oleh,</p>
            <div class="border-t border-slate-900 pt-2 font-bold">Direktur / Owner</div>
        </div>
        <div class="w-48">
            <p class="mb-16">Dibuat Oleh,</p>
            <div class="border-t border-slate-900 pt-2 font-bold">Finance / Admin</div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: none !important; }
        .text-white { color: #0f172a !important; }
        .bg-slate-900 { background-color: #f1f5f9 !important; color: #0f172a !important; border: 2px solid #0f172a; }
    }
</style>
@endsection