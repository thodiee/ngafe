@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6 pb-24 px-4 text-center">
    <!-- Success Celebration Block -->
    <div class="glass-luxury rounded-3xl p-8 border border-brand-500/10 dark:border-brand-500/5 space-y-6">
        <!-- Floating Animated Icon -->
        <div class="w-20 h-20 mx-auto bg-brand-500/10 dark:bg-brand-500/20 text-brand-500 dark:text-brand-400 rounded-full flex items-center justify-center border border-brand-500/20 animate-float shadow-xl shadow-brand-500/10">
            <i class="fa-solid fa-circle-check text-5xl"></i>
        </div>

        <div class="space-y-2">
            <span class="text-xs text-brand-500 font-bold uppercase tracking-widest leading-none">Pembayaran Berhasil</span>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Pesanan Masuk Dapur!</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-[280px] mx-auto leading-relaxed">
                Silakan bersantai di meja Anda. Makanan dan minuman Anda sedang kami persiapkan.
            </p>
        </div>

        <!-- Order Info Card -->
        <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800 rounded-2xl p-4 text-left space-y-2.5">
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-400">Meja:</span>
                <span class="font-bold text-slate-700 dark:text-slate-200 uppercase">MEJA {{ $order->table->table_number ?? 'Dine In' }}</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-400">Nomor Pesanan:</span>
                <span class="font-mono text-slate-600 dark:text-slate-300">{{ $order->order_reference }}</span>
            </div>
            <div class="flex justify-between items-center text-xs border-t border-slate-100 dark:border-slate-800 pt-2.5">
                <span class="text-slate-400 font-bold">Total Pembayaran:</span>
                <span class="font-extrabold text-brand-600 dark:text-brand-400">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Order Summary List -->
    <div class="glass-luxury rounded-3xl p-6 border border-brand-500/10 dark:border-brand-500/5 text-left space-y-4">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ringkasan Pesanan</h3>
        
        <div class="space-y-3">
            @foreach($order->items as $item)
                <div class="flex justify-between items-start gap-4">
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200">
                            {{ $item->product->name }} <span class="font-mono text-[10px] text-slate-400 ml-1">x{{ $item->quantity }}</span>
                        </p>
                        @if($item->variants->count() > 0)
                            <span class="text-[9px] text-slate-400 block leading-tight">
                                {{ $item->variants->pluck('option_name')->join(', ') }}
                            </span>
                        @endif
                        @if($item->notes)
                            <span class="text-[9px] text-orange-500 dark:text-orange-400 block italic leading-tight mt-0.5">
                                <i class="fa-solid fa-comment mr-0.5 text-[8px]"></i> "{{ $item->notes }}"
                            </span>
                        @endif
                    </div>
                    <span class="text-xs font-bold font-mono text-slate-600 dark:text-slate-300">
                        Rp {{ number_format(($item->price + $item->variants->sum('price_adjustment')) * $item->quantity, 0, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Back to Menu button -->
    <div class="px-4">
        <a href="{{ route('self-service.table', $order->table->qr_token) }}" class="w-full bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold py-3.5 rounded-2xl transition flex items-center justify-center gap-2 shadow-md text-sm">
            <i class="fa-solid fa-house"></i> Kembali ke Menu Utama
        </a>
    </div>
</div>
@endsection
