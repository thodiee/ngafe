@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ isset($item) ? 'Edit' : 'Tambah' }} Purchase Order (PO)</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400">Pencatatan pemesanan bahan baku kepada pihak supplier.</p>
</div>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700 max-w-2xl shadow-sm">
    <form action="{{ isset($item) ? route('admin.purchase_orders.update', $item) : route('admin.purchase_orders.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Nomor PO</label>
            <input type="text" name="po_number" value="{{ old('po_number', $item->po_number ?? ('PO-' . date('Ymd') . '-' . rand(100, 999))) }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 font-mono" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Pilih Supplier</label>
            <select name="supplier_id" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
                <option value="">-- Pilih Supplier --</option>
                @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ old('supplier_id', $item->supplier_id ?? '') == $sup->id ? 'selected' : '' }}>
                        {{ $sup->name }} ({{ $sup->phone ?? $sup->contact_person }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-slate-400 text-sm font-medium mb-2">Tanggal PO</label>
                <input type="date" name="po_date" value="{{ old('po_date', $item->po_date ?? date('Y-m-d')) }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
            </div>
            <div>
                <label class="block text-slate-400 text-sm font-medium mb-2">Estimasi Kedatangan</label>
                <input type="date" name="expected_date" value="{{ old('expected_date', $item->expected_date ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-slate-400 text-sm font-medium mb-2">Status PO</label>
                <select name="status" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
                    @foreach(['draft' => 'Draft', 'sent' => 'Dikirim (Sent)', 'partial' => 'Sebagian Diterima', 'completed' => 'Selesai (Completed)', 'cancelled' => 'Dibatalkan'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $item->status ?? 'draft') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-slate-400 text-sm font-medium mb-2">Total Estimasi Nilai (Rp)</label>
                <input type="number" name="total_amount" value="{{ old('total_amount', $item->total_amount ?? 0) }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" min="0">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Catatan Tambahan</label>
            <textarea name="notes" rows="3" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" placeholder="Detail bahan baku, catatan pengiriman, dll.">{{ old('notes', $item->notes ?? '') }}</textarea>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-medium px-6 py-2.5 rounded-lg transition inline-flex items-center gap-2">
                <i class="fa-solid fa-check"></i> Simpan PO
            </button>
            <a href="{{ route('admin.purchase_orders.index') }}" class="text-slate-400 hover:text-white px-4 py-2.5 rounded-lg transition">Batal</a>
        </div>
    </form>
</div>
@endsection
