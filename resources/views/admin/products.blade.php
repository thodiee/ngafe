@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Menu / Produk</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Menu</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nama Menu</th>
                <th class="p-4">Kategori</th>
                <th class="p-4">Harga</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $prod)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4">
                    <span class="font-bold">{{ $prod->name }}</span>
                    @if($prod->is_package)
                        <span class="ml-2 bg-amber-500/20 text-amber-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-amber-500/30"><i class="fa-solid fa-box-open mr-1"></i> Paket</span>
                    @endif
                </td>
                <td class="p-4"><span class="bg-purple-500/20 text-purple-400 px-2 py-1 rounded text-xs font-bold">{{ $prod->category->name ?? 'Uncategorized' }}</span></td>
                <td class="p-4">Rp{{ number_format($prod->price, 0, ',', '.') }}</td>
                <td class="p-4 text-right">
                    <button onclick="openEditModal({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->category_id }}, {{ $prod->price }})" class="text-blue-400 hover:text-blue-300 ml-2"><i class="fa-solid fa-pen-to-square"></i></button>
                    <form action="{{ route('admin.products.destroy', $prod->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 ml-2"><i class="fa-solid fa-trash"></i></button>
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
        <h3 class="text-xl font-bold mb-4">Tambah Menu</h3>
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Menu</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Kategori</label>
                <select name="category_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Harga (Rp)</label>
                <input type="number" name="price" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
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
        <h3 class="text-xl font-bold mb-4">Edit Menu</h3>
        <form id="editForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Menu</label>
                <input type="text" id="edit_name" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Kategori</label>
                <select id="edit_category_id" name="category_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Harga (Rp)</label>
                <input type="number" id="edit_price" name="price" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, catId, price) {
    document.getElementById('editForm').action = '/admin/products/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_category_id').value = catId;
    document.getElementById('edit_price').value = price;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection