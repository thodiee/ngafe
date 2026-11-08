@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                <i class="fa-solid fa-weight-scale text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Master Satuan Ukur</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola satuan takaran yang digunakan pada komposisi resep dan stok inventaris.</p>
            </div>
        </div>
        <div>
            <button onclick="openAddUnitModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-cyan-600/20 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Satuan</span>
            </button>
        </div>
    </div>

    <!-- Units Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden max-w-4xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Nama Satuan</th>
                        <th class="p-4 sm:px-6">Singkatan / Simbol</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($units as $unit)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6 font-bold text-slate-900 dark:text-white">
                            {{ $unit->name }}
                        </td>
                        <td class="p-4 sm:px-6">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                                {{ $unit->abbreviation }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editUnit({{ $unit->id }}, '{{ addslashes($unit->name) }}', '{{ addslashes($unit->abbreviation) }}')" class="p-2 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 hover:bg-brand-500/20 transition" title="Edit Satuan">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.cafe.units.destroy', $unit) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus satuan {{ $unit->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Hapus Satuan">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-scale-unbalanced text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada data satuan terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Satuan -->
<div id="unitModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-sm border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-7 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
            <h3 id="unitModalTitle" class="text-lg font-bold text-slate-900 dark:text-white">Tambah Satuan Ukur</h3>
            <button onclick="closeUnitModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="unitForm" action="{{ route('admin.cafe.units.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="unitMethod" value="POST">
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Satuan</label>
                <input type="text" name="name" id="unitName" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-cyan-500 outline-none transition" placeholder="Contoh: Kilogram / Gram / Mililiter" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Singkatan / Simbol</label>
                <input type="text" name="abbreviation" id="unitAbbr" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-cyan-500 outline-none transition font-mono" placeholder="Contoh: kg / gr / ml" required>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeUnitModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition text-sm">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold shadow-lg shadow-cyan-600/20 active:scale-95 transition text-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddUnitModal() {
    document.getElementById('unitModal').classList.remove('hidden');
    document.getElementById('unitModal').classList.add('flex');
    document.getElementById('unitModalTitle').innerText = 'Tambah Satuan Baru';
    document.getElementById('unitForm').action = "{{ route('admin.cafe.units.store') }}";
    document.getElementById('unitMethod').value = 'POST';
    document.getElementById('unitName').value = '';
    document.getElementById('unitAbbr').value = '';
}

function closeUnitModal() {
    document.getElementById('unitModal').classList.add('hidden');
    document.getElementById('unitModal').classList.remove('flex');
}

function editUnit(id, name, abbr) {
    document.getElementById('unitModal').classList.remove('hidden');
    document.getElementById('unitModal').classList.add('flex');
    document.getElementById('unitModalTitle').innerText = 'Edit Satuan: ' + name;
    document.getElementById('unitForm').action = '/admin/cafe/units/' + id;
    document.getElementById('unitMethod').value = 'PUT';
    document.getElementById('unitName').value = name;
    document.getElementById('unitAbbr').value = abbr;
}
</script>
@endsection
