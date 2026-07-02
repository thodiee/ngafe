@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Kategori Menu</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Kategori</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">ID</th>
                <th class="p-4">Nama Kategori</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4">{{ $cat->id }}</td>
                <td class="p-4 font-bold">{{ $cat->name }}</td>
                <td class="p-4 text-right">
                    <button onclick="openEditModal({{ $cat->id }}, '{{ addslashes($cat->name) }}')" class="text-blue-400 hover:text-blue-300 ml-2"><i class="fa-solid fa-pen-to-square"></i></button>
                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Menghapus kategori akan menghapus produk yang terhubung! Yakin?')">
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
        <h3 class="text-xl font-bold mb-4">Tambah Kategori</h3>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Nama Kategori</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
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
        <h3 class="text-xl font-bold mb-4">Edit Kategori</h3>
        <form id="editForm" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Nama Kategori</label>
                <input type="text" id="edit_name" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name) {
    document.getElementById('editForm').action = '/admin/categories/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection