@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Persetujuan Cuti & Izin</h2>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Tanggal Pengajuan</th>
                <th class="p-4">Pegawai</th>
                <th class="p-4">Jenis</th>
                <th class="p-4">Tanggal Cuti/Izin</th>
                <th class="p-4">Alasan</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaves as $leave)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4">{{ $leave->created_at->format('d M Y H:i') }}</td>
                <td class="p-4 font-bold">{{ $leave->user->name ?? 'Unknown' }}</td>
                <td class="p-4 uppercase text-xs font-bold">{{ $leave->type }}</td>
                <td class="p-4">{{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}</td>
                <td class="p-4 max-w-xs truncate" title="{{ $leave->reason }}">{{ $leave->reason }}</td>
                <td class="p-4">
                    @if($leave->status == 'approved')
                        <span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded text-xs font-bold uppercase">Disetujui</span>
                    @elseif($leave->status == 'rejected')
                        <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs font-bold uppercase">Ditolak</span>
                    @else
                        <span class="bg-orange-500/20 text-orange-400 px-2 py-1 rounded text-xs font-bold uppercase">Pending</span>
                    @endif
                </td>
                <td class="p-4 text-right">
                    @if($leave->status == 'pending')
                    <button onclick="openLeaveModal({{ $leave->id }}, 'approve')" class="text-emerald-400 hover:text-emerald-300 ml-2" title="Setujui"><i class="fa-solid fa-check-circle"></i></button>
                    <button onclick="openLeaveModal({{ $leave->id }}, 'reject')" class="text-red-400 hover:text-red-300 ml-2" title="Tolak"><i class="fa-solid fa-times-circle"></i></button>
                    @else
                    <span class="text-slate-500 text-xs">{{ $leave->hr_notes ?? '-' }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="p-4 text-center text-slate-400">Tidak ada pengajuan cuti/izin.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Approval -->
<div id="leaveModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4" id="modalLeaveTitle">Proses Cuti/Izin</h3>
        <form id="leaveForm" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Catatan HR (Opsional)</label>
                <textarea name="hr_notes" rows="3" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" placeholder="Alasan disetujui / ditolak..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('leaveModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" id="btnSubmitLeave" class="px-4 py-2 text-white font-bold rounded">Konfirmasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openLeaveModal(leaveId, action) {
        const form = document.getElementById('leaveForm');
        const btn = document.getElementById('btnSubmitLeave');
        const title = document.getElementById('modalLeaveTitle');
        
        if (action === 'approve') {
            form.action = "/admin/hr/leaves/" + leaveId + "/approve";
            title.innerText = "Setujui Pengajuan Cuti?";
            btn.className = "px-4 py-2 text-white font-bold rounded bg-emerald-600 hover:bg-emerald-500";
            btn.innerText = "Setujui";
        } else {
            form.action = "/admin/hr/leaves/" + leaveId + "/reject";
            title.innerText = "Tolak Pengajuan Cuti?";
            btn.className = "px-4 py-2 text-white font-bold rounded bg-red-600 hover:bg-red-500";
            btn.innerText = "Tolak";
        }
        
        document.getElementById('leaveModal').classList.remove('hidden');
    }
</script>
@endsection
