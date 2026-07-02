@extends('admin.layout')
@section('content')
@if(session('success'))
<div class="mb-4 bg-emerald-500/20 text-emerald-400 p-4 rounded-xl border border-emerald-500/30"><i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif

<div class="flex justify-between items-center mb-6">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-cyan-500/20 text-cyan-400 rounded-xl flex items-center justify-center text-2xl"><i class="fa-solid fa-weight-scale"></i></div>
        <div>
            <h2 class="text-3xl font-bold">Master Satuan</h2>
            <p class="text-slate-400">Satuan ukur yang digunakan pada resep dan inventori.</p>
        </div>
    </div>
    <button onclick="document.getElementById('addUnitModal').classList.remove('hidden')" class="bg-cyan-600 hover:bg-cyan-500 text-white px-4 py-2 rounded-lg font-bold transition">
        <i class="fa-solid fa-plus mr-2"></i>Tambah Satuan
    </button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400 text-sm">
            <tr>
                <th class="p-4">Nama Satuan</th>
                <th class="p-4">Singkatan</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units as $unit)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold">{{ $unit->name }}</td>
                <td class="p-4"><span class="bg-slate-700 px-3 py-1 rounded-full text-sm text-cyan-400 font-mono">{{ $unit->abbreviation }}</span></td>
                <td class="p-4 text-right">
                    <button onclick="editUnit({{ $unit->id }}, '{{ $unit->name }}', '{{ $unit->abbreviation }}')" class="text-blue-400 hover:text-blue-300 mr-3"><i class="fa-solid fa-edit"></i></button>
                    <form action="{{ route('admin.cafe.units.destroy', $unit) }}" method="POST" class="inline" onsubmit="return confirm('Hapus satuan ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="p-8 text-center text-slate-400">Belum ada satuan. Tambahkan satuan pertama.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Tambah/Edit -->
<div id="addUnitModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-sm border border-slate-700 shadow-2xl">
        <h3 id="unitModalTitle" class="text-xl font-bold mb-5">Tambah Satuan</h3>
        <form id="unitForm" action="{{ route('admin.cafe.units.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="unitMethod" value="POST">
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Satuan</label>
                <input type="text" name="name" id="unitName" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-cyan-500 outline-none transition" placeholder="cth: Kilogram" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Singkatan / Simbol</label>
                <input type="text" name="abbreviation" id="unitAbbr" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-cyan-500 outline-none transition" placeholder="cth: kg" required>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeUnitModal()" class="flex-1 py-3 bg-slate-700 rounded-xl text-white">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUnit(id, name, abbr) {
    document.getElementById('unitModalTitle').innerText = 'Edit Satuan';
    document.getElementById('unitForm').action = '/admin/cafe/units/' + id;
    document.getElementById('unitMethod').value = 'PUT';
    document.getElementById('unitName').value = name;
    document.getElementById('unitAbbr').value = abbr;
    document.getElementById('addUnitModal').classList.remove('hidden');
}
function closeUnitModal() {
    document.getElementById('addUnitModal').classList.add('hidden');
    document.getElementById('unitForm').reset();
    document.getElementById('unitModalTitle').innerText = 'Tambah Satuan';
    document.getElementById('unitForm').action = "{{ route('admin.cafe.units.store') }}";
    document.getElementById('unitMethod').value = 'POST';
}
</script>
@endsection
