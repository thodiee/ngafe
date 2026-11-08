@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-700/60">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-brand-500 flex items-center justify-center text-slate-950 font-bold shadow-md shadow-amber-500/20">
                    <i class="fa-solid fa-map-location-dot text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Master Area Meja</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola zonasi dan pembagian ruangan kafe (Indoor AC, Outdoor, Rooftop, dll.)</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-layer-group text-amber-500"></i> {{ $areas->count() }} Area Terdaftar
            </span>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-600 via-amber-500 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white dark:text-slate-950 font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-amber-500/25 transition text-sm">
                <i class="fa-solid fa-plus"></i> Tambah Area
            </button>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[500px] whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700/80 text-xs uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400">
                        <th class="py-4 px-6 w-20">ID</th>
                        <th class="py-4 px-6">Nama Area Ruangan</th>
                        <th class="py-4 px-6">Kapasitas Meja</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-800 dark:text-slate-200">
                    @forelse($areas as $area)
                    <tr class="hover:bg-amber-500/[0.04] dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-4 px-6 font-mono text-xs text-slate-400">#{{ $area->id }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                <span class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $area->name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-500/20">
                                {{ $area->tables->count() ?? 0 }} Meja
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="inline-flex items-center gap-1">
                                <button onclick="openEditModal({{ $area->id }}, '{{ addslashes($area->name) }}')" class="w-8 h-8 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-slate-700 transition" title="Edit Area">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </button>
                                <form action="{{ route('admin.areas.destroy', $area->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus area ini? (Meja pada area ini akan menjadi tanpa area)')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition" title="Hapus Area">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-slate-400">Belum ada area terdaftar</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900 dark:text-white">Tambah Area Baru</h3>
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.areas.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Nama Area <span class="text-rose-500">*</span></label>
                <input type="text" name="name" placeholder="Contoh: Lantai 1, Outdoor Belakang, VIP Room" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>
            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700/60">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-sm transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white font-bold text-sm shadow-md transition">Simpan Area</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900 dark:text-white">Edit Area</h3>
            <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Nama Area <span class="text-rose-500">*</span></label>
                <input type="text" id="edit_name" name="name" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>
            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700/60">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-sm transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white font-bold text-sm shadow-md transition">Perbarui Area</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name) {
    document.getElementById('editForm').action = '/admin/areas/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection