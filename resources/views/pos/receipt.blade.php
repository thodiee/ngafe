@extends('layouts.app')
@section('content')
<div class="flex justify-center items-center py-10">
    <div id="receiptCard" class="bg-white text-slate-900 p-8 w-full max-w-sm rounded-lg shadow-2xl relative">
        <div class="absolute -top-4 -left-4 w-8 h-8 bg-slate-900 rounded-full"></div>
        <div class="absolute -top-4 -right-4 w-8 h-8 bg-slate-900 rounded-full"></div>
        <div class="absolute -bottom-4 -left-4 w-8 h-8 bg-slate-900 rounded-full"></div>
        <div class="absolute -bottom-4 -right-4 w-8 h-8 bg-slate-900 rounded-full"></div>
        
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold"><i class="fa-solid fa-mug-hot"></i> NGAFE</h2>
            <p class="text-sm text-slate-500">Jl. Suka-Suka No. 1, Jakarta</p>
            <p class="text-sm text-slate-500">Telp: 021-1234567</p>
        </div>
        
        <div class="border-t-2 border-dashed border-slate-300 py-4 mb-4">
            <div class="flex justify-between text-sm">
                <span>Order Ref:</span>
                <span class="font-semibold">{{ $order->order_reference }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Date:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Type:</span>
                <span class="uppercase font-semibold">{{ str_replace('_', ' ', $order->order_type) }}</span>
            </div>
            @if($order->customer)
            <div class="flex justify-between text-sm mt-2 pt-2 border-t border-slate-200">
                <span>Pelanggan:</span>
                <span class="font-semibold">{{ $order->customer->name }}</span>
            </div>
            @endif
        </div>
        
        <div class="border-t-2 border-dashed border-slate-300 py-4 mb-4">
            <table class="w-full text-sm">
                @foreach($order->items as $item)
                <tr>
                    <td class="py-1">{{ $item->product->name ?? 'Product' }}</td>
                    <td class="py-1 text-right">{{ $item->quantity }}x</td>
                    <td class="py-1 text-right">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        
        <div class="border-t-2 border-slate-800 pt-4">
            @if($order->discount_amount > 0)
            <div class="flex justify-between text-sm font-semibold text-slate-600 mb-1">
                <span>Subtotal</span>
                <span>Rp{{ number_format($order->total_amount + $order->discount_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm font-semibold text-slate-600 mb-2">
                <span>Diskon Poin ({{ $order->points_used }} pts)</span>
                <span>-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-lg font-bold">
                <span>TOTAL</span>
                <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm mt-2">
                <span>Payment</span>
                <span class="uppercase">{{ str_replace('_', ' ', $order->payment_method) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Status</span>
                <span class="uppercase font-bold {{ $order->payment_status == 'paid' ? 'text-green-600' : 'text-red-600' }}">{{ $order->payment_status }}</span>
            </div>
        </div>
        
        @if($order->payment_method == 'qris_gateway' && $order->payment_status == 'unpaid')
            <div class="mt-8 text-center">
                <p class="font-bold mb-2">Scan to Pay (Dummy QR)</p>
                <div class="bg-slate-200 w-48 h-48 mx-auto flex items-center justify-center rounded-lg border-4 border-slate-300">
                    <i class="fa-solid fa-qrcode text-6xl text-slate-800"></i>
                </div>
            </div>
        @endif
        
        @if($order->customer)
        <div class="border-t border-dashed border-slate-300 mt-6 pt-4 text-center">
            <div class="text-sm font-bold text-slate-700">Member Loyalty</div>
            @if($order->points_earned > 0)
            <div class="text-xs text-slate-500">Anda mendapatkan <span class="font-bold text-emerald-600">{{ $order->points_earned }} Poin</span> dari transaksi ini.</div>
            @endif
            <div class="text-xs text-slate-500 mt-1">Total Saldo Poin: <span class="font-bold">{{ $order->customer->points }}</span></div>
        </div>
        @endif
        
        <div class="text-center mt-6 text-sm italic text-slate-500">
            Terima kasih atas kunjungan Anda!
        </div>
        
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center no-print">
            <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2 rounded-full text-sm font-bold shadow-md shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Struk
            </button>
            <a href="{{ route('pos.menu') }}" class="bg-slate-900 text-white px-6 py-2 rounded-full text-sm font-bold hover:bg-slate-800 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Menu
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    /* Hide everything on the page */
    body {
        background: white !important;
        color: black !important;
    }
    nav, footer, .mb-4.p-4.rounded-lg, .no-print {
        display: none !important;
    }
    body * {
        visibility: hidden;
    }
    /* Show only the receipt card and its children */
    #receiptCard, #receiptCard * {
        visibility: visible;
    }
    #receiptCard {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: 100%;
        box-shadow: none !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    /* Hide the corner dot decorations for clean printing */
    .absolute.-top-4, .absolute.-bottom-4 {
        display: none !important;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            setTimeout(function() {
                window.print();
            }, 600);
        @endif
    });
</script>
@endsection