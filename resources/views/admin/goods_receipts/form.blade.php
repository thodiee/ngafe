@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ isset($item) ? 'Edit' : 'Tambah' }} Penerimaan Barang (GR)</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400">Pencatatan bukti penerimaan barang masuk dari supplier.</p>
</div>
<div class="bg-slate-800 rounded-2xl p-4 sm:p-6 border border-slate-700 max-w-2xl shadow-sm">
    <form action="{{ isset($item) ? route('admin.goods_receipts.update', $item) : route('admin.goods_receipts.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Nomor GR (Goods Receipt)</label>
            <input type="text" name="gr_number" value="{{ old('gr_number', $item->gr_number ?? ('GR-' . date('Ymd') . '-' . rand(100, 999))) }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 font-mono" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Referensi Purchase Order (Opsional)</label>
            <select name="purchase_order_id" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500">
                <option value="">-- Tanpa PO (Penerimaan Langsung) --</option>
                @foreach($purchase_orders as $po)
                    <option value="{{ $po->id }}" {{ old('purchase_order_id', $item->purchase_order_id ?? '') == $po->id ? 'selected' : '' }}>
                        {{ $po->po_number }} (Tgl: {{ $po->po_date }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Pilih Supplier (Opsional)</label>
            <select name="supplier_id" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500">
                <option value="">-- Pilih Supplier --</option>
                @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ old('supplier_id', $item->supplier_id ?? '') == $sup->id ? 'selected' : '' }}>
                        {{ $sup->name }} ({{ $sup->phone ?? $sup->contact_person }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Tanggal Penerimaan</label>
            <input type="date" name="receipt_date" value="{{ old('receipt_date', $item->receipt_date ?? date('Y-m-d')) }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Catatan Penerimaan / No. Surat Jalan</label>
            <textarea name="notes" rows="3" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" placeholder="Kondisi barang, nomor surat jalan supplier, dll.">{{ old('notes', $item->notes ?? '') }}</textarea>
        </div>
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button type="submit" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white font-medium px-6 py-2.5 rounded-lg transition inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> Simpan Penerimaan
            </button>
            <a href="{{ route('admin.goods_receipts.index') }}" class="w-full sm:w-auto text-center text-slate-400 hover:text-white px-4 py-2.5 rounded-lg transition">Batal</a>
        </div>
    </form>
</div>
@endsection
