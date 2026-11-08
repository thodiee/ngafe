@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    <i class="fa-solid fa-box-open text-sm"></i>
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Inventaris Bahan Baku</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Monitoring stok bahan baku kafe antara gudang penyimpanan dan dapur operasional.</p>
        </div>
        <div>
            <button onclick="document.getElementById('addModal').classList.remove('hidden'); document.getElementById('addModal').classList.add('flex')" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-emerald-600/20 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Bahan Baku</span>
            </button>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    @php
        $lowStockCount = $ingredients->filter(function($i) {
            return $i->stock_gudang <= $i->minimum_stock || $i->stock_dapur <= $i->minimum_stock;
        })->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/60 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Jenis Bahan</div>
            <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ count($ingredients) }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/60 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status Stok Aman</div>
            <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                {{ count($ingredients) - $lowStockCount }}
            </div>
        </div>
        <div class="col-span-2 sm:col-span-1 p-4 rounded-2xl bg-white dark:bg-slate-800/90 border {{ $lowStockCount > 0 ? 'border-red-500/40 bg-red-500/5 dark:bg-red-500/10' : 'border-slate-200 dark:border-slate-700/60' }} shadow-sm">
            <div class="text-xs font-semibold {{ $lowStockCount > 0 ? 'text-red-600 dark:text-red-400 font-bold' : 'text-slate-500 dark:text-slate-400' }}">
                Stok Menipis / Kritis
            </div>
            <div class="text-xl sm:text-2xl font-black {{ $lowStockCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white' }} mt-1">
                {{ $lowStockCount }}
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Nama Bahan</th>
                        <th class="p-4 sm:px-6">Stok Gudang</th>
                        <th class="p-4 sm:px-6">Stok Dapur</th>
                        <th class="p-4 sm:px-6">Batas Minimal</th>
                        <th class="p-4 sm:px-6">Status</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($ingredients as $ing)
                    @php
                        $isLow = $ing->stock_gudang <= $ing->minimum_stock || $ing->stock_dapur <= $ing->minimum_stock;
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $ing->name }}</div>
                            <div class="text-xs text-slate-400">Satuan: {{ $ing->unit }}</div>
                        </td>
                        <td class="p-4 sm:px-6">
                            <div class="inline-flex items-center gap-1.5 font-mono text-xs font-bold px-2.5 py-1 rounded-lg {{ $ing->stock_gudang <= $ing->minimum_stock ? 'bg-red-500/15 text-red-600 dark:text-red-400 border border-red-500/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                                <i class="fa-solid fa-warehouse text-[10px]"></i>
                                <span>{{ $ing->stock_gudang }} {{ $ing->unit }}</span>
                            </div>
                        </td>
                        <td class="p-4 sm:px-6">
                            <div class="inline-flex items-center gap-1.5 font-mono text-xs font-bold px-2.5 py-1 rounded-lg {{ $ing->stock_dapur <= $ing->minimum_stock ? 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }}">
                                <i class="fa-solid fa-fire-burner text-[10px]"></i>
                                <span>{{ $ing->stock_dapur }} {{ $ing->unit }}</span>
                            </div>
                        </td>
                        <td class="p-4 sm:px-6 font-mono text-xs text-slate-500 dark:text-slate-400">
                            {{ $ing->minimum_stock }} {{ $ing->unit }}
                        </td>
                        <td class="p-4 sm:px-6">
                            @if($isLow)
                                <span class="inline-flex items-center gap-1 bg-red-500/15 text-red-600 dark:text-red-400 border border-red-500/30 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Menipis
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Cukup
                                </span>
                            @endif
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openEditModal({{ $ing->id }}, '{{ addslashes($ing->name) }}', {{ $ing->stock_gudang }}, {{ $ing->stock_dapur }}, '{{ addslashes($ing->unit) }}', {{ $ing->minimum_stock }})" class="p-2 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 hover:bg-brand-500/20 transition" title="Edit Bahan">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.ingredients.destroy', $ing->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus bahan baku {{ $ing->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Hapus Bahan">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-box-archive text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada bahan baku terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 my-8 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Tambah Bahan Baku</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden'); document.getElementById('addModal').classList.remove('flex')" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.ingredients.store') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Bahan</label>
                <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" placeholder="Contoh: Susu Full Cream" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Stok Gudang</label>
                    <input type="number" name="stock_gudang" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" value="0" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Satuan (Unit)</label>
                    <input type="text" name="unit" placeholder="gram/ml/pcs" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Batas Minimal Stok (Alert)</label>
                <input type="number" name="minimum_stock" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" value="10" required>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden'); document.getElementById('addModal').classList.remove('flex')" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition text-sm">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold shadow-lg shadow-emerald-600/20 active:scale-95 transition text-sm">
                    Simpan Bahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 my-8 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Edit Bahan Baku</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden'); document.getElementById('editModal').classList.remove('flex')" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editForm" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Bahan</label>
                <input type="text" name="name" id="edit_name" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Stok Gudang</label>
                    <input type="number" name="stock_gudang" id="edit_stock_gudang" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Stok Dapur</label>
                    <input type="number" name="stock_dapur" id="edit_stock_dapur" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Satuan (Unit)</label>
                    <input type="text" name="unit" id="edit_unit" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Min. Stok (Alert)</label>
                    <input type="number" name="minimum_stock" id="edit_min_stock" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition" required>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden'); document.getElementById('editModal').classList.remove('flex')" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition text-sm">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold shadow-lg shadow-emerald-600/20 active:scale-95 transition text-sm">
                    Update Bahan
                </button>
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
    document.getElementById('editModal').classList.add('flex');
}
</script>
@endsection