@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30">
                <i class="fa-solid fa-calendar-days text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Jadwal Shift Mingguan Pegawai</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Atur penugasan shift kerja harian atau gunakan fitur auto-generate jadwal otomatis.</p>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('admin.hr.schedules.auto') }}" method="POST" onsubmit="return confirm('Generate jadwal otomatis untuk minggu ini? (Jadwal yang sudah diisi manual tidak akan ditimpa)')">
                @csrf
                <input type="hidden" name="week" value="{{ $weekStart->format('Y-m-d') }}">
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 px-4 py-2 rounded-xl text-white font-bold text-xs sm:text-sm shadow-md shadow-indigo-600/20 active:scale-95 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                    <span>Auto Generate</span>
                </button>
            </form>
            <form action="{{ route('admin.hr.schedules') }}" method="GET" class="flex items-center gap-2">
                <input type="date" name="week" value="{{ $weekStart->format('Y-m-d') }}" class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs sm:text-sm text-slate-800 dark:text-white outline-none focus:border-brand-500">
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition">
                    Pilih Minggu
                </button>
            </form>
        </div>
    </div>

    <!-- Active Week Tag -->
    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 text-xs font-semibold">
        <i class="fa-solid fa-calendar-week"></i>
        <span>Periode: <b>{{ $weekStart->format('d M Y') }}</b> s/d <b>{{ $weekEnd->format('d M Y') }}</b></span>
    </div>

    <!-- Schedule Matrix Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap min-w-[800px]">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6 border-r border-slate-200 dark:border-slate-700">Pegawai</th>
                        @for($i = 0; $i < 7; $i++)
                            @php $currentDay = $weekStart->copy()->addDays($i); @endphp
                            <th class="p-3 text-center border-r border-slate-200 dark:border-slate-700 last:border-r-0 min-w-[110px]">
                                <span class="block">{{ $currentDay->locale('id')->isoFormat('dddd') }}</span>
                                <span class="text-[11px] font-normal text-slate-400">{{ $currentDay->format('d/m') }}</span>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6 font-bold text-slate-900 dark:text-white border-r border-slate-200 dark:border-slate-700">
                            <div>{{ $user->name }}</div>
                            <span class="text-[11px] font-normal text-slate-400 uppercase">{{ $user->role }}</span>
                        </td>
                        @for($i = 0; $i < 7; $i++)
                            @php 
                                $currentDay = $weekStart->copy()->addDays($i);
                                $schedule = $schedules->where('user_id', $user->id)->where('date', $currentDay->format('Y-m-d'))->first();
                            @endphp
                            <td class="p-2 text-center border-r border-slate-200 dark:border-slate-700 last:border-r-0 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700/50 transition" onclick="openScheduleModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $currentDay->format('Y-m-d') }}', '{{ $schedule ? $schedule->shift_id : '' }}')">
                                @if($schedule)
                                    <div class="bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-xs py-1.5 px-2 rounded-xl font-bold border border-emerald-500/30 shadow-sm">
                                        {{ $schedule->shift->name ?? 'Shift' }}
                                    </div>
                                @else
                                    <div class="text-slate-400 dark:text-slate-500 text-xs italic py-1">
                                        Libur / Off
                                    </div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-calendar-xmark text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada data pegawai untuk dijadwalkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Set Jadwal -->
<div id="scheduleModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-sm border border-slate-200 dark:border-slate-800 shadow-2xl p-6 relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Atur Jadwal Shift</h3>
                <span id="modalUserName" class="text-xs text-brand-600 dark:text-brand-400 font-bold block mt-0.5"></span>
            </div>
            <button onclick="document.getElementById('scheduleModal').classList.add('hidden'); document.getElementById('scheduleModal').classList.remove('flex')" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <p class="text-xs text-slate-400 mt-3" id="modalDateDisplay"></p>
        
        <form action="{{ route('admin.hr.schedules.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="user_id" id="modalUserId">
            <input type="hidden" name="date" id="modalDate">
            
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Pilih Shift Kerja</label>
                <select name="shift_id" id="modalShiftId" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-purple-500 outline-none transition" required>
                    <option value="">-- Pilih Shift --</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('scheduleModal').classList.add('hidden'); document.getElementById('scheduleModal').classList.remove('flex')" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition text-sm">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold shadow-lg shadow-purple-600/20 active:scale-95 transition text-sm">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openScheduleModal(userId, userName, date, shiftId) {
        document.getElementById('modalUserId').value = userId;
        document.getElementById('modalUserName').innerText = userName;
        document.getElementById('modalDate').value = date;
        document.getElementById('modalDateDisplay').innerText = "Tanggal Tugas: " + date;
        document.getElementById('modalShiftId').value = shiftId;
        
        document.getElementById('scheduleModal').classList.remove('hidden');
        document.getElementById('scheduleModal').classList.add('flex');
    }
</script>
@endsection
