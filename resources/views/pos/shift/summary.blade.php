@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto mt-8 mb-12" id="printableArea">
    <div class="bg-white text-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-200">
        <div class="bg-slate-900 p-8 text-center text-white relative">
            <div class="absolute top-4 right-4 print:hidden">
                <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-700 text-white p-3 rounded-lg transition"><i class="fa-solid fa-print"></i></button>
                <a href="{{ route('pos.menu') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white p-3 rounded-lg transition ml-2"><i class="fa-solid fa-house"></i></a>
            </div>
            <i class="fa-solid fa-receipt text-5xl mb-4 text-slate-400"></i>
            <h1 class="text-3xl font-bold uppercase tracking-widest">Shift Summary Report</h1>
            <p class="text-slate-400 mt-2">Sesi ID: #{{ str_pad($session->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
        
        <div class="p-8">
            <div class="grid grid-cols-2 gap-6 mb-8 border-b border-slate-200 pb-8">
                <div>
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Kasir Bertugas</div>
                    <div class="text-lg font-bold">{{ $session->user->name }}</div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Waktu Shift</div>
                    <div class="font-medium">{{ $session->opening_time->format('d M Y, H:i') }} - {{ $session->closing_time->format('H:i') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                    <h3 class="font-bold text-lg mb-4 text-slate-800 border-b border-slate-200 pb-2">Penjualan Total</h3>
                    <div class="flex justify-between mb-2"><span class="text-slate-600">Total Transaksi</span><span class="font-bold">Rp {{ number_format($totalSales, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between mb-2"><span class="text-slate-600">via QRIS / Card</span><span class="font-medium text-blue-600">Rp {{ number_format($qrisSales, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between font-bold mt-4 pt-2 border-t border-slate-200"><span class="text-slate-800">Pendapatan Tunai</span><span class="text-emerald-600">Rp {{ number_format($cashSales, 0, ',', '.') }}</span></div>
                </div>
                
                <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                    <h3 class="font-bold text-lg mb-4 text-slate-800 border-b border-slate-200 pb-2">Rekonsiliasi Laci (Cash)</h3>
                    <div class="flex justify-between mb-2"><span class="text-slate-600">Modal Awal</span><span class="font-bold">Rp {{ number_format($session->opening_cash, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between mb-2"><span class="text-slate-600">Pendapatan Tunai</span><span class="font-bold">Rp {{ number_format($cashSales, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between font-bold mt-4 pt-2 border-t border-slate-200"><span class="text-slate-800">Harapan Laci (Sistem)</span><span>Rp {{ number_format($session->closing_cash_system, 0, ',', '.') }}</span></div>
                    
                    <div class="flex justify-between mb-2 mt-4"><span class="text-slate-600">Fisik di Laci (Aktual)</span><span class="font-bold text-lg">Rp {{ number_format($session->closing_cash_actual, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between font-bold mt-2 pt-2 border-t border-slate-200">
                        <span class="text-slate-800">Selisih (Difference)</span>
                        <span class="text-lg {{ $session->difference == 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format($session->difference, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left mb-8 min-w-[360px]">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-300">
                        <th class="p-3 font-bold text-slate-700">Menu</th>
                        <th class="p-3 font-bold text-slate-700 text-center">Qty</th>
                        <th class="p-3 font-bold text-slate-700 text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productsSold as $item)
                    <tr class="border-b border-slate-100">
                        <td class="p-3 text-slate-800">{{ $item->product->name ?? 'Unknown' }}</td>
                        <td class="p-3 text-center text-slate-600 font-bold">{{ $item->total_qty }}</td>
                        <td class="p-3 text-right text-slate-800 font-bold">{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center text-slate-500 italic">Tidak ada produk terjual.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            
            <div class="text-center text-sm text-slate-500 mt-12 pt-8 border-t border-slate-200">
                Laporan ini dicetak secara otomatis oleh Sistem Ngafe.
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; margin: 0; padding: 0;}
        body { background: white; }
    }
</style>
@endsection