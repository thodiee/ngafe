@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-white"><i class="fa-solid fa-chart-line text-emerald-400 mr-2"></i> Finance Dashboard</h2>
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
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-blue-500 shadow-xl">
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2">Gross Revenue</div>
        <div class="text-2xl font-bold text-blue-400">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-orange-500 shadow-xl">
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2">Est. COGS (HPP)</div>
        <div class="text-2xl font-bold text-orange-400">Rp {{ number_format($cogs, 0, ',', '.') }}</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-purple-500 shadow-xl">
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2">OPEX (Pengeluaran)</div>
        <div class="text-2xl font-bold text-purple-400">Rp {{ number_format($expenses, 0, ',', '.') }}</div>
    </div>
    <div class="bg-slate-800 p-6 rounded-2xl border-t-4 border-emerald-500 shadow-xl relative overflow-hidden">
        <i class="fa-solid fa-coins absolute -right-4 -bottom-4 text-8xl text-emerald-500/10"></i>
        <div class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-2 relative z-10">Net Profit</div>
        <div class="text-3xl font-bold {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }} relative z-10">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 shadow-xl">
        <h3 class="text-lg font-bold mb-4 text-white"><i class="fa-solid fa-chart-pie text-brand-400 mr-2"></i> Laba Kotor (Gross Margin)</h3>
        <div class="text-4xl font-bold text-white mb-2">Rp {{ number_format($grossProfit, 0, ',', '.') }}</div>
        <p class="text-slate-400 text-sm">Pendapatan kotor dikurangi biaya bahan baku. Ini margin kotor sebelum dikurangi operasional seperti gaji dan sewa.</p>
        
        <div class="mt-6">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-slate-300">Margin Persentase</span>
                <span class="font-bold text-white">{{ $grossRevenue > 0 ? round(($grossProfit / $grossRevenue) * 100, 1) : 0 }}%</span>
            </div>
            <div class="w-full bg-slate-700 rounded-full h-2.5">
                <div class="bg-brand-500 h-2.5 rounded-full" style="width: {{ $grossRevenue > 0 ? round(($grossProfit / $grossRevenue) * 100, 1) : 0 }}%"></div>
            </div>
        </div>
    </div>

    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 shadow-xl">
        <h3 class="text-lg font-bold mb-4 text-white"><i class="fa-solid fa-fire text-orange-400 mr-2"></i> Produk Paling Laris (Best Sellers)</h3>
        <div class="space-y-4">
            @forelse($bestSellers as $item)
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-300">{{ $loop->iteration }}</div>
                    <div>
                        <div class="font-bold text-white">{{ $item->product->name ?? 'Unknown' }}</div>
                        <div class="text-xs text-slate-400">{{ $item->total_qty }} terjual</div>
                    </div>
                </div>
                <div class="font-bold text-emerald-400">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</div>
            </div>
            @empty
            <div class="text-center text-slate-500 py-4">Belum ada data penjualan bulan ini.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection