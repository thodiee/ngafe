@extends('admin.layout')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
    <h2 class="text-3xl font-bold">Jadwal Shift Pegawai</h2>
    <div class="flex gap-4">
        <form action="{{ route('admin.hr.schedules.auto') }}" method="POST" onsubmit="return confirm('Generate jadwal otomatis untuk minggu ini? (Jadwal yang sudah diisi manual tidak akan ditimpa)')">
            @csrf
            <input type="hidden" name="week" value="{{ $weekStart->format('Y-m-d') }}">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 px-4 py-2 rounded-lg text-white font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Auto Generate
            </button>
        </form>
        <form action="{{ route('admin.hr.schedules') }}" method="GET" class="flex gap-2">
            <input type="date" name="week" value="{{ $weekStart->format('Y-m-d') }}" class="bg-slate-900 border border-slate-700 rounded px-4 py-2 text-white">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-lg text-white font-bold transition">Cari Minggu</button>
        </form>
    </div>
</div>

<div class="mb-4 text-slate-400">
    Menampilkan jadwal untuk minggu: <span class="font-bold text-white">{{ $weekStart->format('d M Y') }} - {{ $weekEnd->format('d M Y') }}</span>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg overflow-x-auto">
    <table class="w-full text-left whitespace-nowrap">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4 border-r border-slate-700">Pegawai</th>
                @for($i = 0; $i < 7; $i++)
                    @php $currentDay = $weekStart->copy()->addDays($i); @endphp
                    <th class="p-4 text-center border-r border-slate-700">
                        {{ $currentDay->locale('id')->isoFormat('dddd') }}<br>
                        <span class="text-xs">{{ $currentDay->format('d/m') }}</span>
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold border-r border-slate-700">{{ $user->name }}</td>
                @for($i = 0; $i < 7; $i++)
                    @php 
                        $currentDay = $weekStart->copy()->addDays($i);
                        $schedule = $schedules->where('user_id', $user->id)->where('date', $currentDay->format('Y-m-d'))->first();
                    @endphp
                    <td class="p-2 text-center border-r border-slate-700 relative group cursor-pointer hover:bg-slate-700 transition" onclick="openScheduleModal({{ $user->id }}, '{{ $user->name }}', '{{ $currentDay->format('Y-m-d') }}', '{{ $schedule ? $schedule->shift_id : '' }}')">
                        @if($schedule)
                            <div class="bg-emerald-500/20 text-emerald-400 text-xs py-1 px-2 rounded font-bold">
                                {{ $schedule->shift->name ?? 'Unknown Shift' }}
                            </div>
                        @else
                            <div class="text-slate-600 text-xs">Libur/Kosong</div>
                        @endif
                    </td>
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Set Jadwal -->
<div id="scheduleModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-sm border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Set Jadwal: <span id="modalUserName" class="text-emerald-400"></span></h3>
        <p class="text-sm text-slate-400 mb-4" id="modalDateDisplay"></p>
        
        <form action="{{ route('admin.hr.schedules.store') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" id="modalUserId">
            <input type="hidden" name="date" id="modalDate">
            
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Pilih Shift</label>
                <select name="shift_id" id="modalShiftId" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    <option value="">-- Pilih Shift --</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('scheduleModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openScheduleModal(userId, userName, date, shiftId) {
        document.getElementById('modalUserId').value = userId;
        document.getElementById('modalUserName').innerText = userName;
        document.getElementById('modalDate').value = date;
        document.getElementById('modalDateDisplay').innerText = "Tanggal: " + date;
        document.getElementById('modalShiftId').value = shiftId;
        
        document.getElementById('scheduleModal').classList.remove('hidden');
    }
</script>
@endsection
