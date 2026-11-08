@extends('admin.layout')
@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header Section -->
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-brand-500/20 text-brand-500 border border-brand-500/30">
            <i class="fa-solid fa-credit-card text-base"></i>
        </span>
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Gerbang Pembayaran Digital</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Konfigurasi kanal pembayaran otomatis QRIS untuk pemesanan mandiri pelanggan via scan QR meja.</p>
        </div>
    </div>

    <form action="{{ route('admin.cafe.payment_settings.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Mode Integrasi Card --}}
        <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Mode Integrasi Sistem</h3>
            <div class="grid grid-cols-1 gap-3">
                <label class="flex items-start gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-brand-500/50 cursor-pointer transition shadow-sm">
                    <input type="radio" name="midtrans_mode" value="simulation" class="mt-1 text-brand-600 focus:ring-brand-500 shrink-0" {{ $midtransMode === 'simulation' ? 'checked' : '' }}>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm text-slate-900 dark:text-white">Mode Simulasi (Demo / Uji Coba)</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">Rekomendasi Dev</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Mencoba seluruh alur pemesanan mandiri tanpa perlu menghubungkan akun Midtrans. Pelanggan disajikan simulasi QRIS dan dapat menekan tombol bayar sukses untuk mengirim order langsung ke layar dapur.
                        </p>
                    </div>
                </label>

                <label class="flex items-start gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-brand-500/50 cursor-pointer transition shadow-sm">
                    <input type="radio" name="midtrans_mode" value="midtrans" class="mt-1 text-brand-600 focus:ring-brand-500 shrink-0" {{ $midtransMode === 'midtrans' ? 'checked' : '' }}>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm text-slate-900 dark:text-white">Midtrans Snap Gateway (QRIS Resmi)</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">Produksi Otomatis</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Hubungkan akun Midtrans resmi. Pelanggan akan diarahkan ke pop-up pembayaran QRIS nasional (BCA, GoPay, OVO, ShopeePay, Dana, dll) dengan verifikasi status pembayaran instan.
                        </p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Midtrans Credentials Card --}}
        <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kredensial API Midtrans</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Salin Server Key dan Client Key Anda dari dashboard <b>Midtrans</b> &gt; <b>Settings</b> &gt; <b>Access Keys</b>.
            </p>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Midtrans Server Key</label>
                <input type="text" name="midtrans_server_key" value="{{ $serverKey }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm font-mono focus:border-brand-500 outline-none transition" placeholder="SB-Mid-server-xxxxxxxxxxxx">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Midtrans Client Key</label>
                <input type="text" name="midtrans_client_key" value="{{ $clientKey }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm font-mono focus:border-brand-500 outline-none transition" placeholder="SB-Mid-client-xxxxxxxxxxxx">
            </div>

            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                <div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Gunakan Mode Production (Live Money)</span>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jika nonaktif, sistem akan berjalan dalam environment Sandbox (uji coba).</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                    <input type="checkbox" name="midtrans_is_production" value="1" class="sr-only peer" {{ $isProduction ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-emerald-500 rounded-full peer transition-colors"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-500 hover:to-amber-500 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-brand-500/20 active:scale-95 transition text-sm">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Konfigurasi Pembayaran</span>
            </button>
        </div>
    </form>
</div>
@endsection
