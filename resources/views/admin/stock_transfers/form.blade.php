@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ isset($item) ? 'Edit' : 'Tambah' }} Transfer Stok</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400">Pencatatan mutasi perpindahan stok internal antar bagian.</p>
</div>
<div class="bg-slate-800 rounded-2xl p-4 sm:p-6 border border-slate-700 max-w-2xl shadow-sm">
    <form action="{{ isset($item) ? route('admin.stock_transfers.update', $item) : route('admin.stock_transfers.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Nomor Transfer</label>
            <input type="text" name="transfer_number" value="{{ old('transfer_number', $item->transfer_number ?? ('TR-' . date('Ymd') . '-' . rand(100, 999))) }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 font-mono" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Tanggal Transfer</label>
            <input type="date" name="transfer_date" value="{{ old('transfer_date', $item->transfer_date ?? date('Y-m-d')) }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Keterangan / Catatan Transfer</label>
            <textarea name="notes" rows="3" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" placeholder="Misal: Dari Gudang Utama ke Bar Dapur untuk persiapan shift sore.">{{ old('notes', $item->notes ?? '') }}</textarea>
        </div>
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button type="submit" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white font-medium px-6 py-2.5 rounded-lg transition inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> Simpan Transfer
            </button>
            <a href="{{ route('admin.stock_transfers.index') }}" class="w-full sm:w-auto text-center text-slate-400 hover:text-white px-4 py-2.5 rounded-lg transition">Batal</a>
        </div>
    </form>
</div>
@endsection
