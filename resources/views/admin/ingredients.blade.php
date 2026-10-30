@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Inventaris (Bahan Baku)</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Bahan</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nama Bahan</th>
                <th class="p-4">Stok Saat Ini</th>
                <th class="p-4">Minimal Stok</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ingredients as $ing)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold">{{ $ing->name }}</td>
                <td class="p-4">
                    Gudang: <span class="{{ $ing->stock_gudang <= $ing->minimum_stock ? 'text-red-400 font-bold' : 'text-emerald-400' }}">{{ $ing->stock_gudang }}</span> {{ $ing->unit }}<br>
                    Dapur: <span class="{{ $ing->stock_dapur <= $ing->minimum_stock ? 'text-red-400 font-bold' : 'text-emerald-400' }}">{{ $ing->stock_dapur }}</span> {{ $ing->unit }}
                </td>
                <td class="p-4">{{ $ing->minimum_stock }} {{ $ing->unit }}</td>
                <td class="p-4 text-right">
                    <button onclick="openEditModal({{ $ing->id }}, '{{ addslashes($ing->name) }}', {{ $ing->stock_gudang }}, {{ $ing->stock_dapur }}, '{{ addslashes($ing->unit) }}', {{ $ing->minimum_stock }})" class="text-blue-400 hover:text-blue-300 ml-2"><i class="fa-solid fa-edit"></i></button>
                    <form action="{{ route('admin.ingredients.destroy', $ing->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus bahan baku ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 ml-2"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Tambah Bahan Baku</h3>
        <form action="{{ route('admin.ingredients.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Bahan</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label class="block text-sm mb-1 text-slate-400">Stok Gudang</label>
                    <input type="number" name="stock_gudang" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm mb-1 text-slate-400">Satuan (Unit)</label>
                    <input type="text" name="unit" placeholder="gram/ml/pcs" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Batas Minimal Stok (Notifikasi)</label>
                <input type="number" name="minimum_stock" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Edit Bahan Baku</h3>
        <form id="editForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Bahan</label>
                <input type="text" name="name" id="edit_name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label class="block text-sm mb-1 text-slate-400">Stok Gudang</label>
                    <input type="number" name="stock_gudang" id="edit_stock_gudang" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm mb-1 text-slate-400">Stok Dapur</label>
                    <input type="number" name="stock_dapur" id="edit_stock_dapur" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label class="block text-sm mb-1 text-slate-400">Satuan (Unit)</label>
                    <input type="text" name="unit" id="edit_unit" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm mb-1 text-slate-400">Min Stok</label>
                    <input type="number" name="minimum_stock" id="edit_min_stock" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, stock_gudang, stock_dapur, unit, min_stock) {
    document.getElementById('editForm').action = '/admin/ingredients/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_stock_gudang').value = stock_gudang;
    document.getElementById('edit_stock_dapur').value = stock_dapur;
    document.getElementById('edit_unit').value = unit;
    document.getElementById('edit_min_stock').value = min_stock;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection