@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ isset($item) ? 'Edit' : 'Tambah' }} GoodsReceipt</h2>
</div>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700 max-w-2xl">
    <form action="{{ isset($item) ? route('admin.goods_receipts.update', $item) : route('admin.goods_receipts.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Gr number</label>
            <input type="text" name="gr_number" value="{{ old('gr_number', $item->gr_number ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Purchase order id</label>
            <input type="text" name="purchase_order_id" value="{{ old('purchase_order_id', $item->purchase_order_id ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Receipt date</label>
            <input type="text" name="receipt_date" value="{{ old('receipt_date', $item->receipt_date ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-lg transition">Simpan</button>
            <a href="{{ route('admin.goods_receipts.index') }}" class="ml-4 text-slate-400 hover:text-white">Batal</a>
        </div>
    </form>
</div>
@endsection
