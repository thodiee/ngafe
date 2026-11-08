@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30">
                <i class="fa-solid fa-clock text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Master Shift Kerja</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Konfigurasi jadwal jam kerja operasional kafe (Shift Pagi, Middle, Malam).</p>
            </div>
        </div>
        <div>
            <button onclick="openAddModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-purple-600/20 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Shift</span>
            </button>
        </div>
    </div>

    <!-- Shifts Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden max-w-4xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap min-w-[560px]">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Nama Shift</th>
                        <th class="p-4 sm:px-6">Jam Mulai</th>
                        <th class="p-4 sm:px-6">Jam Selesai</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($shifts as $shift)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6 font-bold text-slate-900 dark:text-white">
                            {{ $shift->name }}
                        </td>
                        <td class="p-4 sm:px-6 font-mono text-emerald-600 dark:text-emerald-400 font-semibold">
                            <i class="fa-solid fa-play text-[10px] mr-1.5 opacity-60"></i>
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} WIB
                        </td>
                        <td class="p-4 sm:px-6 font-mono text-orange-600 dark:text-orange-400 font-semibold">
                            <i class="fa-solid fa-stop text-[10px] mr-1.5 opacity-60"></i>
                            {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }} WIB
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editShift({{ $shift->id }}, '{{ addslashes($shift->name) }}', '{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}', '{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}')" class="p-2 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 hover:bg-brand-500/20 transition" title="Edit Shift">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.hr.shifts.destroy', $shift->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus shift {{ $shift->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Hapus Shift">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-clock text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada data shift kerja. Tambahkan shift pertama.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Shift -->
<div id="addModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-7 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modalTitle">Tambah Master Shift</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.hr.shifts.store') }}" method="POST" id="shiftForm" class="mt-5 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Shift</label>
                <input type="text" name="name" id="name" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-purple-500 outline-none transition" placeholder="Contoh: Shift Pagi (Opening)" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Jam Mulai</label>
                    <input type="time" name="start_time" id="start_time" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-purple-500 outline-none transition font-mono" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Jam Selesai</label>
                    <input type="time" name="end_time" id="end_time" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-purple-500 outline-none transition font-mono" required>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition text-sm">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold shadow-lg shadow-purple-600/20 active:scale-95 transition text-sm">
                    Simpan Shift
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('addModal').classList.add('flex');
        document.getElementById('shiftForm').reset();
        document.getElementById('shiftForm').action = "{{ route('admin.hr.shifts.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('modalTitle').innerText = "Tambah Master Shift";
    }

    function closeModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.getElementById('addModal').classList.remove('flex');
    }

    function editShift(id, name, startTime, endTime) {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('addModal').classList.add('flex');
        document.getElementById('modalTitle').innerText = "Edit Shift: " + name;
        document.getElementById('shiftForm').action = "/admin/hr/shifts/" + id;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('name').value = name;
        document.getElementById('start_time').value = startTime;
        document.getElementById('end_time').value = endTime;
    }
</script>
@endsection
