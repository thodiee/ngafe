@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ isset($item) ? 'Edit' : 'Tambah' }} Stok Opname</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400">Pencatatan sesi audit fisik persediaan bahan baku.</p>
</div>
<div class="bg-slate-800 rounded-2xl p-4 sm:p-6 border border-slate-700 max-w-2xl shadow-sm">
    <form action="{{ isset($item) ? route('admin.stock_opnames.update', $item) : route('admin.stock_opnames.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Nomor Opname</label>
            <input type="text" name="opname_number" value="{{ old('opname_number', $item->opname_number ?? ('SO-' . date('Ymd') . '-' . rand(100, 999))) }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 font-mono" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Tanggal Opname</label>
            <input type="date" name="opname_date" value="{{ old('opname_date', $item->opname_date ?? date('Y-m-d')) }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-slate-400 text-sm font-medium mb-2">Lokasi / Area Stok</label>
                <select name="location" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
                    <option value="gudang" {{ old('location', $item->location ?? 'gudang') == 'gudang' ? 'selected' : '' }}>Gudang Utama (Storage)</option>
                    <option value="dapur" {{ old('location', $item->location ?? '') == 'dapur' ? 'selected' : '' }}>Dapur / Bar (Kitchen)</option>
                </select>
            </div>
            <div>
                <label class="block text-slate-400 text-sm font-medium mb-2">Status Audit</label>
                <select name="status" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
                    <option value="draft" {{ old('status', $item->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft (Proses Penghitungan)</option>
                    <option value="completed" {{ old('status', $item->status ?? '') == 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Catatan Hasil Opname</label>
            <textarea name="notes" rows="3" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" placeholder="Keterangan selisih, petugas opname, dll.">{{ old('notes', $item->notes ?? '') }}</textarea>
        </div>
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button type="submit" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white font-medium px-6 py-2.5 rounded-lg transition inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> Simpan Stok Opname
            </button>
            <a href="{{ route('admin.stock_opnames.index') }}" class="w-full sm:w-auto text-center text-slate-400 hover:text-white px-4 py-2.5 rounded-lg transition">Batal</a>
        </div>
    </form>
</div>
@endsection
