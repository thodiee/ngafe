@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6 pb-24 px-4 text-center">
    <!-- Pending Status Block -->
    <div class="glass-luxury rounded-3xl p-8 border border-brand-500/10 dark:border-brand-500/5 space-y-6">
        <!-- Hourglass Pulsing Icon -->
        <div class="w-20 h-20 mx-auto bg-amber-500/10 dark:bg-amber-500/20 text-amber-500 dark:text-amber-400 rounded-full flex items-center justify-center border border-amber-500/20 animate-pulse shadow-xl shadow-amber-500/5">
            <i class="fa-solid fa-hourglass-half text-4xl animate-spin" style="animation-duration: 4s;"></i>
        </div>

        <div class="space-y-2">
            <span class="text-xs text-amber-600 dark:text-amber-400 font-bold uppercase tracking-widest leading-none">Menunggu Pembayaran</span>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Silakan Bayar di Kasir</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-[300px] mx-auto leading-relaxed">
                Tunjukkan halaman ini atau sebutkan nomor meja Anda ke kasir untuk menyelesaikan pembayaran.
            </p>
        </div>

        <!-- Waiting Indicator -->
        <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/40 rounded-2xl py-3 px-4 flex items-center justify-center gap-3">
            <i class="fa-solid fa-circle-notch animate-spin text-amber-600 dark:text-amber-400 text-xs"></i>
            <span class="text-xs font-semibold text-amber-800 dark:text-amber-300">Menunggu konfirmasi kasir...</span>
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
                <span class="text-slate-400 font-bold">Harus Dibayar:</span>
                <span class="font-extrabold text-brand-600 dark:text-brand-400 text-base">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
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
        <a href="{{ route('self-service.table', $order->table->qr_token) }}" class="w-full bg-slate-950 hover:bg-slate-900 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold py-3.5 rounded-2xl transition flex items-center justify-center gap-2 shadow-md text-sm">
            <i class="fa-solid fa-house"></i> Kembali ke Menu Utama
        </a>
    </div>
</div>

<script>
    // AJAX Polling to check if payment status has changed to "paid"
    const pollInterval = setInterval(() => {
        fetch(window.location.href, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.payment_status === 'paid') {
                clearInterval(pollInterval);
                // Redirect to success page
                window.location.href = '{{ route("self-service.payment-success", $order->order_reference) }}';
            }
        })
        .catch(err => console.error('Polling error:', err));
    }, 4000); // Poll every 4 seconds
</script>
@endsection
