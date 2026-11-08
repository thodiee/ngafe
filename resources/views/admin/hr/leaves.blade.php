@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30">
                <i class="fa-solid fa-envelope-open-text text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Persetujuan Cuti & Izin</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Tinjau permohonan cuti, sakit, dan izin tidak masuk kerja dari para staf.</p>
            </div>
        </div>
        @php
            $pendingLeavesCount = $leaves->where('status', 'pending')->count();
        @endphp
        @if($pendingLeavesCount > 0)
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30 text-xs font-bold self-start sm:self-auto">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>{{ $pendingLeavesCount }} Pengajuan Menunggu Konfirmasi</span>
        </div>
        @endif
    </div>

    <!-- Leaves Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Tgl Pengajuan</th>
                        <th class="p-4 sm:px-6">Pegawai</th>
                        <th class="p-4 sm:px-6">Jenis Izin</th>
                        <th class="p-4 sm:px-6">Rentang Tanggal</th>
                        <th class="p-4 sm:px-6">Alasan</th>
                        <th class="p-4 sm:px-6 text-center">Status</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($leaves as $leave)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6 text-xs text-slate-500 dark:text-slate-400 font-mono">
                            {{ $leave->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="p-4 sm:px-6 font-bold text-slate-900 dark:text-white">
                            {{ $leave->user->name ?? 'Unknown' }}
                            <span class="text-xs font-normal text-slate-400 block uppercase">{{ $leave->user->role ?? '' }}</span>
                        </td>
                        <td class="p-4 sm:px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                {{ $leave->type }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6 text-xs text-slate-700 dark:text-slate-300 font-semibold">
                            {{ $leave->start_date->format('d M Y') }} s/d {{ $leave->end_date->format('d M Y') }}
                        </td>
                        <td class="p-4 sm:px-6 text-xs text-slate-600 dark:text-slate-400 max-w-xs truncate" title="{{ $leave->reason }}">
                            {{ $leave->reason }}
                        </td>
                        <td class="p-4 sm:px-6 text-center">
                            @if($leave->status == 'approved')
                                <span class="inline-flex items-center gap-1 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 px-2.5 py-1 rounded-full text-xs font-bold uppercase">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui
                                </span>
                            @elseif($leave->status == 'rejected')
                                <span class="inline-flex items-center gap-1 bg-red-500/15 text-red-600 dark:text-red-400 border border-red-500/30 px-2.5 py-1 rounded-full text-xs font-bold uppercase">
                                    <i class="fa-solid fa-circle-xmark text-[10px]"></i> Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30 px-2.5 py-1 rounded-full text-xs font-bold uppercase">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            @if($leave->status == 'pending')
                            <div class="flex items-center justify-end gap-1.5">
                                <button onclick="openLeaveModal({{ $leave->id }}, 'approve')" class="p-2 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 transition" title="Setujui Cuti">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <button onclick="openLeaveModal({{ $leave->id }}, 'reject')" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Tolak Pengajuan">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            @else
                            <span class="text-slate-400 text-xs italic">{{ $leave->hr_notes ?? '-' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-envelope-open text-4xl mb-3 opacity-30"></i>
                            <p>Tidak ada pengajuan cuti atau izin staf.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Approval -->
<div id="leaveModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-7 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modalLeaveTitle">Proses Permohonan Cuti</h3>
            <button onclick="closeLeaveModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="leaveForm" method="POST" class="mt-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Catatan HRD / Alasan</label>
                <textarea name="hr_notes" rows="3" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition resize-none" placeholder="Tuliskan catatan persetujuan atau alasan penolakan..."></textarea>
            </div>
            
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeLeaveModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition text-sm">
                    Batal
                </button>
                <button type="submit" id="btnSubmitLeave" class="px-5 py-2.5 rounded-xl text-white font-bold shadow-lg active:scale-95 transition text-sm">
                    Simpan Keputusan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openLeaveModal(id, action) {
        const form = document.getElementById('leaveForm');
        const title = document.getElementById('modalLeaveTitle');
        const btn = document.getElementById('btnSubmitLeave');
        
        if (action === 'approve') {
            form.action = '/admin/hr/leaves/' + id + '/approve';
            title.innerText = 'Setujui Pengajuan Cuti';
            btn.className = 'px-5 py-2.5 rounded-xl text-white font-bold shadow-lg active:scale-95 transition text-sm bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-emerald-600/20';
            btn.innerText = 'Setujui Cuti';
        } else {
            form.action = '/admin/hr/leaves/' + id + '/reject';
            title.innerText = 'Tolak Pengajuan Cuti';
            btn.className = 'px-5 py-2.5 rounded-xl text-white font-bold shadow-lg active:scale-95 transition text-sm bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 shadow-red-600/20';
            btn.innerText = 'Tolak Pengajuan';
        }
        
        document.getElementById('leaveModal').classList.remove('hidden');
        document.getElementById('leaveModal').classList.add('flex');
    }

    function closeLeaveModal() {
        document.getElementById('leaveModal').classList.add('hidden');
        document.getElementById('leaveModal').classList.remove('flex');
    }
</script>
@endsection
