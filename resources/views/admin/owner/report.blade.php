@extends('admin.layout')
@section('content')

{{-- FILTER BAR --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-amber-400 via-orange-400 to-rose-500">
            <i class="fa-solid fa-crown mr-3 text-amber-400"></i>Laporan Owner
        </h1>
        <p class="text-slate-400 mt-1">Ringkasan lengkap bisnis — {{ $startDate->locale('id')->isoFormat('MMMM YYYY') }}</p>
    </div>
    <form action="{{ route('admin.owner.report') }}" method="GET" class="flex gap-2 items-end">
        <div>
            <label class="block text-xs text-slate-400 mb-1">Bulan</label>
            <select name="month" class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm">
                @for($i=1;$i<=12;$i++)
                    <option value="{{ str_pad($i,2,'0',STR_PAD_LEFT) }}" {{ $month == str_pad($i,2,'0',STR_PAD_LEFT) ? 'selected':'' }}>{{ date('F',mktime(0,0,0,$i,10)) }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-400 mb-1">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm w-24">
        </div>
        <button type="submit" class="bg-amber-600 hover:bg-amber-500 px-4 py-2 rounded-lg text-white font-bold text-sm transition">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>
    </form>
</div>

{{-- WARNING BANNER FOR EMPTY DATA / UNOPENED CASHIER --}}
@if($totalOrders == 0 && $totalSessions == 0)
<div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-5 rounded-2xl mb-8 flex items-start gap-4">
    <div class="w-12 h-12 bg-rose-500/20 text-rose-400 rounded-xl flex items-center justify-center shrink-0">
        <i class="fa-solid fa-cash-register text-xl"></i>
    </div>
    <div>
        <h4 class="font-bold text-lg text-white">Belum Buka Kasir / Belum Ada Data Masuk</h4>
        <p class="text-sm text-slate-400 mt-1">
            Belum ada sesi kasir yang dibuka atau data transaksi penjualan yang masuk untuk periode ini. Seluruh data laporan saat ini masih bernilai nol.
        </p>
    </div>
</div>
@elseif($totalOrders == 0)
<div class="bg-amber-500/10 border border-amber-500/20 text-amber-400 p-5 rounded-2xl mb-8 flex items-start gap-4">
    <div class="w-12 h-12 bg-amber-500/20 text-amber-400 rounded-xl flex items-center justify-center shrink-0">
        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
    </div>
    <div>
        <h4 class="font-bold text-lg text-white">Belum Ada Transaksi Penjualan</h4>
        <p class="text-sm text-slate-400 mt-1">
            Sudah ada sesi kasir yang dibuka, namun belum ada transaksi penjualan berstatus lunas (paid) yang tercatat untuk periode ini.
        </p>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- 1. KPI CARDS ─ TOP ROW --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $kpis = [
            ['label'=>'Total Pendapatan','value'=>'Rp '.number_format($grossRevenue,0,',','.'),'icon'=>'fa-sack-dollar','color'=>'emerald','sub'=>$totalOrders.' transaksi'],
            ['label'=>'Laba Bersih','value'=>'Rp '.number_format($netProfit,0,',','.'),'icon'=>'fa-chart-line','color'=>$netProfit>=0?'blue':'red','sub'=>'Margin '.$margin.'%'],
            ['label'=>'Total Biaya','value'=>'Rp '.number_format($totalExpenses,0,',','.'),'icon'=>'fa-money-bill-wave','color'=>'rose','sub'=>'HPP + Operasional'],
            ['label'=>'Rata-rata Transaksi','value'=>'Rp '.number_format($avgOrderValue,0,',','.'),'icon'=>'fa-receipt','color'=>'violet','sub'=>'per pesanan'],
        ];
    @endphp
    @foreach($kpis as $kpi)
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-{{ $kpi['color'] }}-500/20 text-{{ $kpi['color'] }}-400 rounded-xl flex items-center justify-center">
                <i class="fa-solid {{ $kpi['icon'] }}"></i>
            </div>
            <span class="text-xs text-slate-400 uppercase tracking-wide">{{ $kpi['label'] }}</span>
        </div>
        <div class="text-2xl font-bold text-white">{{ $kpi['value'] }}</div>
        <div class="text-xs text-slate-500 mt-1">{{ $kpi['sub'] }}</div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- 2. GRAFIK PENDAPATAN HARIAN --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <div class="xl:col-span-2 bg-slate-800 border border-slate-700 rounded-2xl p-6">
        <h3 class="font-bold mb-4 text-slate-200"><i class="fa-solid fa-chart-area mr-2 text-emerald-400"></i>Pendapatan Harian</h3>
        <canvas id="dailyChart" height="90"></canvas>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6">
        <h3 class="font-bold mb-4 text-slate-200"><i class="fa-solid fa-chart-pie mr-2 text-rose-400"></i>Breakdown Biaya</h3>
        <canvas id="expenseChart" height="200"></canvas>
        <div class="mt-4 space-y-2">
            @foreach($expenseByCategory->take(5) as $exp)
            <div class="flex justify-between text-xs">
                <span class="text-slate-400">{{ $exp->category }}</span>
                <span class="text-white font-bold">Rp {{ number_format($exp->total,0,',','.') }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- 3. GRAFIK BULANAN YTD --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 mb-6">
    <h3 class="font-bold mb-4 text-slate-200"><i class="fa-solid fa-chart-bar mr-2 text-violet-400"></i>Pendapatan Bulanan — {{ $year }}</h3>
    <canvas id="monthlyChart" height="60"></canvas>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- 4. PRODUK TERLARIS + JAM TERSIBUK --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6">
        <h3 class="font-bold mb-4 text-slate-200"><i class="fa-solid fa-fire-flame-curved mr-2 text-orange-400"></i>Produk Terlaris</h3>
        <div class="space-y-3">
            @forelse($topProducts as $i => $tp)
            @php
                $maxQty = $topProducts->first()->qty;
                $pct = $maxQty > 0 ? ($tp->qty / $maxQty * 100) : 0;
                $colors = ['bg-amber-500','bg-orange-500','bg-rose-500','bg-violet-500','bg-blue-500'];
            @endphp
            <div class="flex items-center gap-3">
                <span class="w-6 text-center text-xs font-bold {{ $i < 3 ? 'text-amber-400' : 'text-slate-500' }}">#{{ $i+1 }}</span>
                <div class="flex-1">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium truncate">{{ $tp->product->name ?? 'Produk #'.$tp->product_id }}</span>
                        <span class="text-xs text-slate-400 ml-2">{{ $tp->qty }}x</span>
                    </div>
                    <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full {{ $colors[$i % 5] }} rounded-full transition-all" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                <span class="text-xs text-emerald-400 font-bold text-right min-w-[90px]">Rp {{ number_format($tp->revenue,0,',','.') }}</span>
            </div>
            @empty
            <div class="text-center py-8 text-slate-500">Tidak ada data penjualan.</div>
            @endforelse
        </div>
    </div>

    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 flex flex-col gap-4">
        {{-- Jam Tersibuk --}}
        <div>
            <h3 class="font-bold mb-3 text-slate-200"><i class="fa-solid fa-clock mr-2 text-cyan-400"></i>Jam Tersibuk</h3>
            <div class="flex gap-3">
                @forelse($peakHours as $i => $ph)
                <div class="flex-1 bg-slate-900 rounded-xl p-3 text-center border border-slate-700">
                    <div class="text-2xl font-bold text-cyan-400">{{ str_pad($ph->hour,2,'0',STR_PAD_LEFT) }}:00</div>
                    <div class="text-xs text-slate-400 mt-1">{{ $ph->cnt }} pesanan</div>
                    <div class="text-xs text-amber-400 font-bold mt-1">#{{ $i+1 }} Tersibuk</div>
                </div>
                @empty
                <p class="text-slate-500 text-sm">Belum ada data.</p>
                @endforelse
            </div>
        </div>
        {{-- POS Sessions --}}
        <div>
            <h3 class="font-bold mb-3 text-slate-200"><i class="fa-solid fa-cash-register mr-2 text-emerald-400"></i>Sesi Kasir</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-900 rounded-xl p-4 border border-slate-700 text-center flex flex-col justify-center items-center">
                    <div class="text-2xl font-bold text-white">
                        @if($totalSessions == 0)
                            <span class="text-xs text-rose-400 font-bold bg-rose-500/10 px-2.5 py-1 rounded-full border border-rose-500/20">Belum Buka Kasir</span>
                        @else
                            {{ $totalSessions }}
                        @endif
                    </div>
                    <div class="text-xs text-slate-400 mt-1">Total Sesi Dibuka</div>
                </div>
                <div class="bg-slate-900 rounded-xl p-4 border border-slate-700 text-center">
                    <div class="text-2xl font-bold {{ $avgDifference < 0 ? 'text-red-400' : 'text-emerald-400' }}">
                        Rp {{ number_format(abs($avgDifference),0,',','.') }}
                    </div>
                    <div class="text-xs text-slate-400 mt-1">Rata-rata Selisih Kasir</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- 5. PEGAWAI --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Ringkasan Pegawai --}}
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6">
        <h3 class="font-bold mb-4 text-slate-200"><i class="fa-solid fa-users mr-2 text-purple-400"></i>Ringkasan Kepegawaian</h3>
        <div class="grid grid-cols-2 gap-4 mb-4">
            @php
                $empKpis = [
                    ['label'=>'Total Pegawai','val'=>$totalEmployees,'icon'=>'fa-user-tie','c'=>'purple'],
                    ['label'=>'Total Kehadiran','val'=>$totalAttendances.' hari','icon'=>'fa-fingerprint','c'=>'emerald'],
                    ['label'=>'Tingkat Hadir','val'=>$attendanceRate.'%','icon'=>'fa-percent','c'=>'blue'],
                    ['label'=>'Keterlambatan','val'=>$lateCount.' kali','icon'=>'fa-clock','c'=>'orange'],
                ];
            @endphp
            @foreach($empKpis as $e)
            <div class="bg-slate-900 rounded-xl p-4 border border-slate-700">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fa-solid {{ $e['icon'] }} text-{{ $e['c'] }}-400 text-sm"></i>
                    <span class="text-xs text-slate-400">{{ $e['label'] }}</span>
                </div>
                <div class="text-xl font-bold text-white">{{ $e['val'] }}</div>
            </div>
            @endforeach
        </div>

        @if($topLate->count() > 0)
        <div>
            <h4 class="text-xs text-slate-500 uppercase tracking-wider mb-2">Pegawai Sering Terlambat</h4>
            <div class="space-y-2">
                @foreach($topLate as $tl)
                <div class="flex items-center justify-between bg-slate-900 px-4 py-2 rounded-lg border border-slate-700">
                    <span class="text-sm font-medium">{{ $tl->user->name ?? 'User' }}</span>
                    <span class="bg-orange-500/20 text-orange-400 px-2 py-0.5 rounded text-xs font-bold">{{ $tl->cnt }}x</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Payroll Summary --}}
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6">
        <h3 class="font-bold mb-4 text-slate-200"><i class="fa-solid fa-file-invoice-dollar mr-2 text-emerald-400"></i>Ringkasan Penggajian Bulan Ini</h3>
        @if($payrollSummary && $payrollSummary->total_employees > 0)
        <div class="space-y-4">
            <div class="bg-slate-900 rounded-xl p-5 border border-slate-700 text-center">
                <div class="text-3xl font-bold text-emerald-400">Rp {{ number_format($payrollSummary->total_payroll, 0, ',', '.') }}</div>
                <div class="text-sm text-slate-400 mt-1">Total Pengeluaran Gaji</div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-900 rounded-xl p-4 border border-slate-700 text-center">
                    <div class="text-xl font-bold">{{ $payrollSummary->total_employees }}</div>
                    <div class="text-xs text-slate-400 mt-1">Pegawai Ter-generate</div>
                </div>
                <div class="bg-slate-900 rounded-xl p-4 border border-slate-700 text-center">
                    <div class="text-xl font-bold text-blue-400">Rp {{ number_format($payrollSummary->avg_salary, 0, ',', '.') }}</div>
                    <div class="text-xs text-slate-400 mt-1">Rata-rata Gaji Bersih</div>
                </div>
            </div>
        </div>
        @else
        <div class="flex flex-col items-center justify-center h-40 text-slate-500">
            <i class="fa-solid fa-file-circle-xmark text-4xl mb-3 opacity-30"></i>
            <p class="text-sm">Payroll belum di-generate untuk bulan ini.</p>
            <a href="{{ route('admin.hr.payroll') }}" class="mt-3 text-xs text-emerald-400 underline">Generate Payroll</a>
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- 6. INVENTORI --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 mb-4">
        <div>
            <h3 class="font-bold text-slate-200"><i class="fa-solid fa-triangle-exclamation mr-2 text-red-400"></i>Stok Mendekati Habis ({{ $lowStockCount }} item)</h3>
            <p class="text-xs text-slate-400 mt-1"><i class="fa-solid fa-info-circle mr-1"></i>Catatan Rumus: Kritis jika (Stok Gudang + Stok Dapur) &le; Stok Minimum</p>
        </div>
        <a href="{{ route('admin.ingredients.index') }}" class="text-xs text-blue-400 hover:underline self-start md:self-auto">Kelola Inventori →</a>
    </div>
    @if($lowStock->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-slate-400 border-b border-slate-700">
                <tr>
                    <th class="pb-2 px-2">Bahan</th>
                    <th class="pb-2 px-2">Stok Gudang</th>
                    <th class="pb-2 px-2">Stok Dapur</th>
                    <th class="pb-2 px-2">Minimum</th>
                    <th class="pb-2 px-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStock as $item)
                @php $total = $item->stock_gudang + $item->stock_dapur; @endphp
                <tr class="border-b border-slate-700/40 hover:bg-slate-700/20">
                    <td class="py-2 px-2 font-medium">{{ $item->name }}</td>
                    <td class="py-2 px-2">{{ $item->stock_gudang }} {{ $item->unit }}</td>
                    <td class="py-2 px-2">{{ $item->stock_dapur }} {{ $item->unit }}</td>
                    <td class="py-2 px-2">{{ $item->minimum_stock }} {{ $item->unit }}</td>
                    <td class="py-2 px-2">
                        @if($total <= 0)
                            <span class="bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-xs font-bold">HABIS</span>
                        @else
                            <span class="bg-orange-500/20 text-orange-400 px-2 py-0.5 rounded text-xs font-bold">KRITIS</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-8 text-emerald-400"><i class="fa-solid fa-circle-check text-3xl mb-2 block"></i>Semua stok dalam kondisi aman.</div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- CHARTS (Chart.js) --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<script>
const chartDefaults = {
    responsive: true,
    plugins: { legend: { labels: { color: '#94a3b8', font: { size: 11 } } } },
    scales: {
        x: { ticks: { color: '#64748b' }, grid: { color: '#1e293b' } },
        y: { ticks: { color: '#64748b', callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' }, grid: { color: '#1e293b' } }
    }
};

// DAILY REVENUE
const dailyLabels = @json($dailyRevenue->keys()->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m')));
const dailyData   = @json($dailyRevenue->values());
new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{ label: 'Pendapatan', data: dailyData, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.4, pointRadius: 3 }]
    },
    options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, tooltip: { callbacks: { label: c => 'Rp ' + Number(c.raw).toLocaleString('id-ID') } } } }
});

// MONTHLY REVENUE
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const monthlyRawData = @json($monthlyRevenue);
const monthlyData = months.map((_, i) => monthlyRawData[i+1] ?? 0);
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{ label: 'Pendapatan', data: monthlyData, backgroundColor: 'rgba(139,92,246,0.7)', borderRadius: 6 }]
    },
    options: { ...chartDefaults }
});

// EXPENSE BREAKDOWN PIE
const expLabels  = @json($expenseByCategory->pluck('category'));
const expData    = @json($expenseByCategory->pluck('total'));
const expColors  = ['#ef4444','#f97316','#eab308','#22c55e','#06b6d4','#8b5cf6','#ec4899'];
new Chart(document.getElementById('expenseChart'), {
    type: 'doughnut',
    data: {
        labels: expLabels,
        datasets: [{ data: expData, backgroundColor: expColors, borderWidth: 0, hoverOffset: 4 }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 10 }, padding: 8 } },
        tooltip: { callbacks: { label: c => c.label + ': Rp ' + Number(c.raw).toLocaleString('id-ID') } } }
    }
});
</script>
@endsection
