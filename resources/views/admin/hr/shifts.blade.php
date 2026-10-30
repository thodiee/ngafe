@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Master Shift</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Shift</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nama Shift</th>
                <th class="p-4">Jam Mulai</th>
                <th class="p-4">Jam Selesai</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shifts as $shift)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold">{{ $shift->name }}</td>
                <td class="p-4">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</td>
                <td class="p-4">{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</td>
                <td class="p-4 text-right">
                    <button onclick="editShift({{ $shift->id }}, '{{ $shift->name }}', '{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}', '{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}')" class="text-brand-400 hover:text-brand-300 ml-2"><i class="fa-solid fa-edit"></i></button>
                    <form action="{{ route('admin.hr.shifts.destroy', $shift->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus shift ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 ml-2"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah/Edit -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4" id="modalTitle">Tambah Master Shift</h3>
        <form action="{{ route('admin.hr.shifts.store') }}" method="POST" id="shiftForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Shift (Cth: Pagi)</label>
                <input type="text" name="name" id="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm mb-1 text-slate-400">Jam Mulai</label>
                    <input type="time" name="start_time" id="start_time" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
                <div>
                    <label class="block text-sm mb-1 text-slate-400">Jam Selesai</label>
                    <input type="time" name="end_time" id="end_time" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.getElementById('shiftForm').reset();
        document.getElementById('shiftForm').action = "{{ route('admin.hr.shifts.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('modalTitle').innerText = "Tambah Master Shift";
    }

    function editShift(id, name, start, end) {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('modalTitle').innerText = "Edit Master Shift";
        document.getElementById('shiftForm').action = "/admin/hr/shifts/" + id;
        document.getElementById('formMethod').value = "PUT";
        
        document.getElementById('name').value = name;
        document.getElementById('start_time').value = start;
        document.getElementById('end_time').value = end;
    }
</script>
@endsection
