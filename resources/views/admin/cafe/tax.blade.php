@extends('admin.layout')
@section('content')
@if(session('success'))
<div class="mb-4 bg-emerald-500/20 text-emerald-400 p-4 rounded-xl border border-emerald-500/30"><i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif

<div class="flex items-center gap-4 mb-8">
    <div class="w-12 h-12 bg-red-500/20 text-red-400 rounded-xl flex items-center justify-center text-2xl"><i class="fa-solid fa-receipt"></i></div>
    <div>
        <h2 class="text-3xl font-bold">Konfigurasi Pajak & Biaya Layanan</h2>
        <p class="text-slate-400">Atur PPN dan service charge yang diterapkan pada setiap transaksi.</p>
    </div>
</div>

<form action="{{ route('admin.cafe.tax.update') }}" method="POST" class="max-w-2xl space-y-5">
    @csrf

    {{-- Pajak / PPN --}}
    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-lg">Pajak (PPN)</h3>
                <p class="text-sm text-slate-400">Dikenakan kepada pelanggan berdasarkan total transaksi.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="tax_enabled" id="taxEnabled" value="1" class="sr-only peer" {{ $taxEnabled ? 'checked' : '' }}>
                <div class="w-12 h-6 bg-slate-600 peer-checked:bg-red-500 rounded-full peer transition-colors"></div>
                <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-6"></div>
            </label>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Label Pajak</label>
                <input type="text" name="tax_label" value="{{ $taxLabel }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-red-500 outline-none transition" placeholder="PPN">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Tarif Pajak (%)</label>
                <input type="number" name="tax_rate" value="{{ $taxRate }}" step="0.01" min="0" max="100" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-red-500 outline-none transition" placeholder="11">
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between bg-slate-900 px-4 py-3 rounded-xl border border-slate-700">
            <div>
                <span class="text-sm font-medium">Pajak Inklusif (sudah termasuk harga)</span>
                <p class="text-xs text-slate-500">Jika aktif, harga produk sudah termasuk pajak.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="tax_inclusive" value="1" class="sr-only peer" {{ $taxInclusive ? 'checked' : '' }}>
                <div class="w-10 h-5 bg-slate-600 peer-checked:bg-red-500 rounded-full peer transition-colors"></div>
                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
            </label>
        </div>
    </div>

    {{-- Service Charge --}}
    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-lg">Biaya Layanan (Service Charge)</h3>
                <p class="text-sm text-slate-400">Biaya tambahan atas layanan yang diberikan.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="service_charge_enabled" value="1" class="sr-only peer" {{ $serviceCharge ? 'checked' : '' }}>
                <div class="w-12 h-6 bg-slate-600 peer-checked:bg-amber-500 rounded-full peer transition-colors"></div>
                <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-6"></div>
            </label>
        </div>
        <div>
            <label class="block text-sm text-slate-400 mb-1">Tarif Biaya Layanan (%)</label>
            <input type="number" name="service_charge_rate" value="{{ $serviceRate }}" step="0.01" min="0" max="100" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 outline-none transition" placeholder="5">
        </div>
    </div>

    {{-- Preview --}}
    <div class="bg-slate-900 p-5 rounded-2xl border border-slate-700 text-sm">
        <h4 class="font-bold mb-3 text-slate-300"><i class="fa-solid fa-calculator mr-2 text-slate-400"></i>Simulasi Perhitungan (Subtotal Rp 100.000)</h4>
        <div class="space-y-2 text-slate-400">
            <div class="flex justify-between"><span>Subtotal</span><span class="text-white font-mono">Rp 100.000</span></div>
            <div class="flex justify-between"><span>Service Charge ({{ $serviceRate }}%)</span><span class="text-amber-400 font-mono">+ Rp {{ number_format($serviceRate * 1000, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>PPN {{ $taxRate }}%</span><span class="text-red-400 font-mono">+ Rp {{ number_format(($taxRate/100) * 100000, 0, ',', '.') }}</span></div>
            <div class="flex justify-between border-t border-slate-700 pt-2 font-bold text-white">
                <span>Total</span>
                <span class="font-mono text-emerald-400">Rp {{ number_format(100000 + ($serviceRate * 1000) + (($taxRate/100) * 100000), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
        <i class="fa-solid fa-save"></i> Simpan Konfigurasi Pajak
    </button>
</form>
@endsection
