@extends('admin.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ isset($item) ? 'Edit' : 'Tambah' }} Resep Bahan (BOM)</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400">Pilih produk dan bahan baku serta jumlah takaran yang dibutuhkan.</p>
</div>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700 max-w-2xl shadow-sm">
    <form action="{{ isset($item) ? route('admin.recipes.update', $item) : route('admin.recipes.store') }}" method="POST">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Produk Menu</label>
            <select name="product_id" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
                <option value="">-- Pilih Produk Menu --</option>
                @foreach($products as $prod)
                    <option value="{{ $prod->id }}" {{ old('product_id', $item->product_id ?? '') == $prod->id ? 'selected' : '' }}>
                        {{ $prod->name }} (Rp {{ number_format($prod->price, 0, ',', '.') }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Bahan Baku</label>
            <select name="ingredient_id" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
                <option value="">-- Pilih Bahan Baku --</option>
                @foreach($ingredients as $ing)
                    <option value="{{ $ing->id }}" {{ old('ingredient_id', $item->ingredient_id ?? '') == $ing->id ? 'selected' : '' }}>
                        {{ $ing->name }} (Satuan: {{ $ing->unit }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-slate-400 text-sm font-medium mb-2">Jumlah Takaran Per Porsi</label>
            <input type="number" step="0.001" min="0.001" name="quantity" value="{{ old('quantity', $item->quantity ?? '') }}" placeholder="Contoh: 18 atau 0.25" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500" required>
            <p class="text-xs text-slate-500 mt-1">Masukkan takaran bahan baku (misal: gram, ml, atau pcs).</p>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-medium px-6 py-2.5 rounded-lg transition inline-flex items-center gap-2">
                <i class="fa-solid fa-check"></i> Simpan Resep
            </button>
            <a href="{{ route('admin.recipes.index') }}" class="text-slate-400 hover:text-white px-4 py-2.5 rounded-lg transition">Batal</a>
        </div>
    </form>
</div>
@endsection
