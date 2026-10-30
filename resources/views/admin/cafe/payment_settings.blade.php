@extends('admin.layout')

@section('content')
@if(session('success'))
<div class="mb-4 bg-emerald-500/20 text-emerald-400 p-4 rounded-xl border border-emerald-500/30"><i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif

<div class="flex items-center gap-4 mb-8">
    <div class="w-12 h-12 bg-brand-500/20 text-brand-400 rounded-xl flex items-center justify-center text-2xl"><i class="fa-solid fa-credit-card"></i></div>
    <div>
        <h2 class="text-3xl font-bold">Konfigurasi Gerbang Pembayaran</h2>
        <p class="text-slate-400">Atur integrasi gerbang pembayaran online (QRIS) untuk pemesanan mandiri meja.</p>
    </div>
</div>

<form action="{{ route('admin.cafe.payment_settings.update') }}" method="POST" class="max-w-2xl space-y-5">
    @csrf

    {{-- Mode Pembayaran --}}
    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
        <h3 class="font-bold text-lg mb-4">Mode Integrasi</h3>
        <div class="space-y-4">
            <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-900 border border-slate-700 hover:border-brand-500/50 cursor-pointer transition">
                <input type="radio" name="midtrans_mode" value="simulation" class="mt-1 text-brand-600 focus:ring-brand-500" {{ $midtransMode === 'simulation' ? 'checked' : '' }}>
                <div>
                    <span class="font-bold text-sm text-slate-100">Mode Simulasi (Demo / Development)</span>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Gunakan mode ini untuk mencoba seluruh alur pemesanan mandiri tanpa perlu menghubungkan akun Midtrans. Pelanggan akan melihat halaman QRIS simulasi dan dapat menekan tombol bayar sukses untuk mengirim pesanan langsung ke dapur.
                    </p>
                </div>
            </label>

            <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-900 border border-slate-700 hover:border-brand-500/50 cursor-pointer transition">
                <input type="radio" name="midtrans_mode" value="midtrans" class="mt-1 text-brand-600 focus:ring-brand-500" {{ $midtransMode === 'midtrans' ? 'checked' : '' }}>
                <div>
                    <span class="font-bold text-sm text-slate-100">Midtrans Gateway (QRIS Otomatis)</span>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Hubungkan dengan gerbang pembayaran Midtrans. Pelanggan akan membayar menggunakan QRIS resmi melalui antarmuka Midtrans Snap. Kunci API (Server Key & Client Key) wajib diisi.
                    </p>
                </div>
            </label>
        </div>
    </div>

    {{-- Kredensial Midtrans --}}
    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4">
        <h3 class="font-bold text-lg">Kredensial API Midtrans</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
            Dapatkan Server Key dan Client Key Anda dari dashboard **Midtrans** -> **Settings** -> **Access Keys**.
        </p>

        <div>
            <label class="block text-sm text-slate-400 mb-1">Midtrans Server Key</label>
            <input type="text" name="midtrans_server_key" value="{{ $serverKey }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-brand-500 outline-none transition" placeholder="SB-Mid-server-xxxxxxxxxxxx">
        </div>

        <div>
            <label class="block text-sm text-slate-400 mb-1">Midtrans Client Key</label>
            <input type="text" name="midtrans_client_key" value="{{ $clientKey }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-brand-500 outline-none transition" placeholder="SB-Mid-client-xxxxxxxxxxxx">
        </div>

        <div class="flex items-center justify-between bg-slate-900 px-4 py-3 rounded-xl border border-slate-700">
            <div>
                <span class="text-sm font-medium text-slate-200">Gunakan Mode Production (Live)</span>
                <p class="text-xs text-slate-500">Jika mati, transaksi akan diproses sebagai Sandbox (uji coba).</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="midtrans_is_production" value="1" class="sr-only peer" {{ $isProduction ? 'checked' : '' }}>
                <div class="w-10 h-5 bg-slate-600 peer-checked:bg-emerald-500 rounded-full peer transition-colors"></div>
                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
            </label>
        </div>
    </div>

    <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-brand-500/10">
        <i class="fa-solid fa-save"></i> Simpan Konfigurasi Pembayaran
    </button>
</form>
@endsection
