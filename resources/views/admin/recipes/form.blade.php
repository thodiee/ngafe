@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ isset($item) ? 'Edit' : 'Tambah' }} Recipe</h2>
</div>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700 max-w-2xl">
    <form action="{{ isset($item) ? route('admin.recipes.update', $item) : route('admin.recipes.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Product id</label>
            <input type="text" name="product_id" value="{{ old('product_id', $item->product_id ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Ingredient id</label>
            <input type="text" name="ingredient_id" value="{{ old('ingredient_id', $item->ingredient_id ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 mb-2">Quantity</label>
            <input type="text" name="quantity" value="{{ old('quantity', $item->quantity ?? '') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500" required>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-lg transition">Simpan</button>
            <a href="{{ route('admin.recipes.index') }}" class="ml-4 text-slate-400 hover:text-white">Batal</a>
        </div>
    </form>
</div>
@endsection
