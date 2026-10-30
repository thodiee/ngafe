@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold">Promo Ulang Tahun</h2>
    <p class="text-slate-400">Atur fitur pemberian hadiah atau promo khusus untuk pelanggan yang berulang tahun.</p>
</div>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700 max-w-2xl">
    <form action="{{ route('admin.settings.birthday.update') }}" method="POST">
        @csrf
        
        <div class="mb-6 p-4 bg-slate-900 border border-amber-500/50 rounded-lg">
            <h3 class="font-bold text-lg mb-4 text-amber-400">Status Fitur Promo Ulang Tahun</h3>
            <div class="flex items-center space-x-6">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="bday_promo_status" value="1" {{ $bday_promo_status->value == '1' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                    <span class="text-slate-300">Aktif (Berikan Promo)</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="bday_promo_status" value="0" {{ $bday_promo_status->value == '0' ? 'checked' : '' }} class="text-slate-500 focus:ring-slate-500">
                    <span class="text-slate-300">Tidak Aktif</span>
                </label>
            </div>
            <p class="text-xs text-slate-500 mt-2">Jika aktif, kasir akan menerima notifikasi di layar POS saat member yang ulang tahun dipilih.</p>
        </div>
        
        <div class="mb-6 p-4 bg-slate-900 border border-slate-700 rounded-lg">
            <h3 class="font-bold text-lg mb-2 text-white">Pesan Hadiah / Promo</h3>
            <p class="text-sm text-slate-400 mb-4">Tuliskan hadiah yang harus diberikan kasir (contoh: Gratis Kopi Susu, atau Diskon 50%). Pesan ini akan muncul di layar POS.</p>
            <label class="block text-slate-400 mb-2 font-semibold">Teks Promo</label>
            <input type="text" name="bday_promo_message" value="{{ old('bday_promo_message', $bday_promo_message->value) }}" class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-amber-500" required>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white px-6 py-3 rounded-xl transition font-bold w-full">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
