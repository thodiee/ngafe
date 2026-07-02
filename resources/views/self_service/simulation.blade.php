@extends('layouts.app')

@section('content')
@php
    $serverKey = \App\Models\Setting::get('midtrans_server_key', '');
    $grossAmount = $order->total_amount;
    $statusCode = '200';
    $signature = hash('sha512', $order->order_reference . $statusCode . $grossAmount . $serverKey);
@endphp

<div class="max-w-md mx-auto space-y-6 pb-24 px-4">
    <!-- QRIS Simulated Header -->
    <div class="glass-luxury rounded-3xl p-6 border border-brand-500/10 dark:border-brand-500/5 text-center space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-1.5 text-slate-800 dark:text-white font-extrabold text-lg tracking-wider">
                <span class="text-brand-500 font-black">QR</span><span>IS</span>
            </div>
            <span class="text-[10px] bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                Mode Simulasi
            </span>
        </div>

        <div class="space-y-1">
            <p class="text-xs text-slate-400">Total Pembayaran</p>
            <h2 class="text-3xl font-extrabold text-brand-600 dark:text-brand-400">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </h2>
            <p class="text-xs text-slate-500 font-mono">Ref: {{ $order->order_reference }}</p>
        </div>

        <!-- Simulated QRIS Frame -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-inner max-w-[280px] mx-auto space-y-3">
            <div class="flex justify-between items-center text-[10px] font-bold text-slate-400">
                <span>NGAFE COFFEE</span>
                <span>NMID: ID1020304050</span>
            </div>
            <div class="bg-slate-50 p-2 rounded-xl flex justify-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=Ngafe-Simulated-QRIS-{{ $order->order_reference }}" 
                     alt="Simulated QRIS QR Code" 
                     class="w-48 h-48 mix-blend-multiply">
            </div>
            <div class="text-[9px] text-slate-400 leading-tight">
                Pindai QR ini menggunakan aplikasi e-wallet Anda untuk simulasi pembayaran.
            </div>
        </div>

        <!-- Timer -->
        <div class="bg-slate-50 dark:bg-slate-900/60 rounded-2xl py-2 px-4 inline-flex items-center gap-2 border border-slate-100 dark:border-slate-800">
            <i class="fa-regular fa-clock text-amber-500 animate-pulse text-xs"></i>
            <span class="text-xs text-slate-600 dark:text-slate-400">Sisa waktu bayar: <strong class="font-mono text-slate-700 dark:text-slate-300" id="countdown">14:59</strong></span>
        </div>
    </div>

    <!-- Instructions & Action Button -->
    <div class="glass-luxury p-6 rounded-3xl border border-brand-500/10 dark:border-brand-500/5 space-y-4">
        <div class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
            <p class="font-bold text-slate-800 dark:text-white mb-1">Cara Pembayaran Simulasi:</p>
            <div class="flex gap-2">
                <span class="w-5 h-5 rounded-full bg-brand-500/10 text-brand-500 font-bold flex items-center justify-center flex-shrink-0">1</span>
                <span>Klik tombol <strong>Konfirmasi Simulasi Sukses</strong> di bawah ini.</span>
            </div>
            <div class="flex gap-2">
                <span class="w-5 h-5 rounded-full bg-brand-500/10 text-brand-500 font-bold flex items-center justify-center flex-shrink-0">2</span>
                <span>Sistem akan mengirimkan webhook pembayaran tiruan (settlement) ke aplikasi.</span>
            </div>
            <div class="flex gap-2">
                <span class="w-5 h-5 rounded-full bg-brand-500/10 text-brand-500 font-bold flex items-center justify-center flex-shrink-0">3</span>
                <span>Pesanan Anda otomatis akan dikirim ke antrean dapur.</span>
            </div>
        </div>

        <button onclick="simulateSuccessPayment()" id="btnSimulate" class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-3.5 rounded-2xl transition flex items-center justify-center gap-2 shadow-lg shadow-brand-500/20 text-sm">
            <i class="fa-solid fa-circle-check"></i> Konfirmasi Simulasi Sukses
        </button>
    </div>
</div>

<script>
    // Countdown Timer logic
    let time = 15 * 60; // 15 minutes
    const timerEl = document.getElementById('countdown');
    const interval = setInterval(() => {
        time--;
        if (time <= 0) {
            clearInterval(interval);
            timerEl.innerText = "EXPIRED";
            document.getElementById('btnSimulate').disabled = true;
            return;
        }
        const min = Math.floor(time / 60);
        const sec = time % 60;
        timerEl.innerText = `${min.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
    }, 1000);

    // Simulated payment webhook triggering
    function simulateSuccessPayment() {
        const btn = document.getElementById('btnSimulate');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Mengirim Callback Simulasi...';

        fetch('{{ route("self-service.midtrans-webhook") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                order_id: '{{ $order->order_reference }}',
                status_code: '200',
                gross_amount: '{{ $grossAmount }}',
                signature_key: '{{ $signature }}',
                transaction_status: 'settlement',
                payment_type: 'qris'
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Callback gagal diterima server.');
            }
            return response.json();
        })
        .then(data => {
            // Callback simulation was successful, wait 1.5 seconds then redirect to success screen
            setTimeout(() => {
                window.location.href = '{{ route("self-service.payment-success", $order->order_reference) }}';
            }, 1000);
        })
        .catch(err => {
            console.error(err);
            alert('Gagal menyimulasikan pembayaran: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Konfirmasi Simulasi Sukses';
        });
    }
</script>
@endsection
