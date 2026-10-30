@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-white"><i class="fa-solid fa-chart-bar text-blue-400 mr-2"></i> Laporan Penjualan (Sales Report)</h2>
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
        <button type="button" onclick="window.print()" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg transition ml-2 print:hidden"><i class="fa-solid fa-print"></i> Cetak</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 print:hidden">
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-blue-500 shadow-xl">
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2">Total Item Terjual</div>
        <div class="text-4xl font-bold text-blue-400">{{ $totalItems }} Porsi</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-emerald-500 shadow-xl">
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2">Total Omset Penjualan</div>
        <div class="text-4xl font-bold text-emerald-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
    </div>
</div>

<div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden shadow-xl" id="printableArea">
    <div class="p-6 border-b border-slate-700 hidden print:block text-center text-slate-900">
        <h2 class="text-2xl font-bold mb-2">Laporan Penjualan (Produk Terjual)</h2>
        <p>Periode: {{ date('F Y', mktime(0,0,0,$month,10,$year)) }}</p>
    </div>
    
    <div class="p-6 border-b border-slate-700 print:hidden flex justify-between items-center">
        <h3 class="font-bold text-lg text-white">Rincian Produk Terjual ({{ date('F Y', mktime(0,0,0,$month,10,$year)) }})</h3>
        <span class="text-sm text-slate-400">Diurutkan berdasarkan produk terlaris</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left print:text-slate-900">
            <thead>
                <tr class="bg-slate-900/50 print:bg-slate-200 text-slate-400 print:text-slate-800 text-sm border-b border-slate-700 print:border-slate-400">
                    <th class="p-4 font-medium">Peringkat</th>
                    <th class="p-4 font-medium">Nama Produk</th>
                    <th class="p-4 font-medium text-center">Kategori</th>
                    <th class="p-4 font-medium text-center">Qty Terjual</th>
                    <th class="p-4 font-medium text-right">Total Omset (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productsSold as $item)
                <tr class="border-b border-slate-700/50 print:border-slate-300 hover:bg-slate-700/20 transition">
                    <td class="p-4 text-slate-300 print:text-slate-800 font-bold">#{{ $loop->iteration }}</td>
                    <td class="p-4 text-white print:text-slate-900 font-bold">{{ $item->product->name ?? 'Unknown' }}</td>
                    <td class="p-4 text-center text-sm">
                        <span class="bg-slate-700 print:bg-slate-200 text-slate-300 print:text-slate-800 px-2 py-1 rounded">{{ $item->product->category->name ?? '-' }}</span>
                    </td>
                    <td class="p-4 text-center font-bold text-blue-400 print:text-blue-800">{{ $item->total_qty }}</td>
                    <td class="p-4 font-bold text-emerald-400 print:text-emerald-800 text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">Tidak ada data penjualan di bulan ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if($productsSold->count() > 0)
            <tfoot class="bg-slate-900 print:bg-slate-200 border-t border-slate-700 print:border-slate-400">
                <tr>
                    <th colspan="3" class="p-4 text-right text-slate-300 print:text-slate-800 uppercase tracking-wider font-bold">Total Penjualan Keseluruhan</th>
                    <th class="p-4 text-center text-blue-400 print:text-blue-800 font-bold text-lg">{{ $totalItems }}</th>
                    <th class="p-4 text-right text-emerald-400 print:text-emerald-800 font-bold text-lg">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: none !important; background: white;}
        .bg-slate-800 { background: white !important; }
        .text-white { color: #0f172a !important; }
    }
</style>
@endsection