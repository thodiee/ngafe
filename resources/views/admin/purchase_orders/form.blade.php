@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ isset($item) ? 'Edit' : 'Tambah' }} PurchaseOrder</h2>
</div>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700 max-w-2xl">
    <form action="{{ isset($item) ? route('admin.purchase_orders.update', $item) : route('admin.purchase_orders.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Po number</label>
            <input type="text" name="po_number" value="{{ old('po_number', $item->po_number ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Supplier id</label>
            <input type="text" name="supplier_id" value="{{ old('supplier_id', $item->supplier_id ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Po date</label>
            <input type="text" name="po_date" value="{{ old('po_date', $item->po_date ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Status</label>
            <input type="text" name="status" value="{{ old('status', $item->status ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Total amount</label>
            <input type="text" name="total_amount" value="{{ old('total_amount', $item->total_amount ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-lg transition">Simpan</button>
            <a href="{{ route('admin.purchase_orders.index') }}" class="ml-4 text-slate-400 hover:text-white">Batal</a>
        </div>
    </form>
</div>
@endsection
