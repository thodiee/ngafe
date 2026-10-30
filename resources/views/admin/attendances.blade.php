@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Manajemen Absensi</h2>
    <form action="{{ route('admin.attendances.bulk_destroy_proof') }}" method="POST" class="flex items-center gap-2 bg-slate-800 p-2 rounded-xl border border-slate-700" onsubmit="return confirm('Yakin ingin menghapus foto absensi lama? Data absensi akan tetap utuh.');">
        @csrf
        <select name="months" class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg px-3 py-1.5 focus:ring-red-500 focus:border-red-500 outline-none">
            <option value="1">Lebih dari 1 Bulan</option>
            <option value="2">Lebih dari 2 Bulan</option>
            <option value="3">Lebih dari 3 Bulan</option>
            <option value="6">Lebih dari 6 Bulan</option>
            <option value="12">Lebih dari 1 Tahun</option>
        </select>
        <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1.5 rounded-lg text-sm font-bold transition flex items-center gap-2">
            <i class="fa-solid fa-broom"></i> Bersihkan Foto
        </button>
    </form>
</div>

@if(session('success'))
    <div class="mb-4 bg-emerald-500/20 text-emerald-400 p-4 rounded-xl border border-emerald-500/30">
        <i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

{{-- VALIDASI WAJAH PENDING --}}
@if($pendingFaceUsers->count() > 0)
<div class="mb-6 bg-orange-500/10 border border-orange-500/30 rounded-2xl p-5">
    <h3 class="font-bold text-orange-400 mb-3 flex items-center gap-2">
        <i class="fa-solid fa-face-smile text-xl"></i>
        Menunggu Validasi Wajah ({{ $pendingFaceUsers->count() }})
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($pendingFaceUsers as $u)
        <div class="bg-slate-800 rounded-xl p-4 flex items-center justify-between border border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-500/20 rounded-full flex items-center justify-center font-bold text-orange-400">
                    {{ strtoupper(substr($u->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-bold text-sm">{{ $u->name }}</div>
                    <div class="text-xs text-slate-400 capitalize">{{ $u->role }}</div>
                </div>
            </div>
            <form action="{{ route('admin.users.verify_face', $u->id) }}" method="POST" onsubmit="return confirm('Validasi wajah {{ $u->name }}?')">
                @csrf
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                    <i class="fa-solid fa-check mr-1"></i>Verifikasi
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- RIWAYAT ABSENSI --}}
<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg overflow-x-auto">
    <table class="w-full text-left whitespace-nowrap">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400 text-sm">
            <tr>
                <th class="p-4">Tanggal</th>
                <th class="p-4">Pegawai</th>
                <th class="p-4">Clock In</th>
                <th class="p-4">Clock Out</th>
                <th class="p-4">Status</th>
                <th class="p-4">Lokasi</th>
                <th class="p-4">Foto Bukti</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $att)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                <td class="p-4 font-bold">{{ $att->user->name ?? 'Unknown' }}</td>
                <td class="p-4 text-emerald-400 font-mono">{{ $att->clock_in ?? '-' }}</td>
                <td class="p-4 text-orange-400 font-mono">{{ $att->clock_out ?? '-' }}</td>
                <td class="p-4">
                    @if($att->status == 'ontime')
                        <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs font-bold">Tepat Waktu</span>
                    @else
                        <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs font-bold">Terlambat</span>
                    @endif
                </td>
                <td class="p-4">
                    @if($att->latitude && $att->longitude)
                        <a href="https://www.google.com/maps?q={{ $att->latitude }},{{ $att->longitude }}" target="_blank" class="text-blue-400 hover:text-blue-300 text-xs underline flex items-center gap-1">
                            <i class="fa-solid fa-map-location-dot"></i> Maps
                        </a>
                    @else
                        <span class="text-slate-500 text-xs">-</span>
                    @endif
                </td>
                <td class="p-4">
                    @if($att->photo_proof)
                        <div class="flex items-center gap-2">
                            <button onclick="document.getElementById('proof-{{ $att->id }}').classList.remove('hidden')" class="text-blue-400 text-xs underline hover:text-blue-300">
                                <i class="fa-solid fa-image mr-1"></i>Lihat
                            </button>
                            <form action="{{ route('admin.attendances.destroy_proof', $att->id) }}" method="POST" onsubmit="return confirm('Hapus foto selfie ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 text-xs underline hover:text-red-300"><i class="fa-solid fa-times mr-1"></i>Hapus Foto</button>
                            </form>
                        </div>
                        <div id="proof-{{ $att->id }}" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center hidden">
                            <div class="bg-slate-800 p-4 rounded-2xl border border-slate-700 max-w-xs w-full relative">
                                <button onclick="document.getElementById('proof-{{ $att->id }}').classList.add('hidden')" class="absolute top-3 right-3 text-slate-400 hover:text-white"><i class="fa-solid fa-times"></i></button>
                                <h4 class="font-bold mb-3 text-sm">Foto Bukti - {{ $att->user->name }}</h4>
                                <img src="{{ $att->photo_proof }}" class="w-full rounded-xl" alt="Foto Bukti">
                                <p class="text-xs text-slate-400 mt-2 text-center">{{ $att->date }} • {{ $att->clock_in }}</p>
                            </div>
                        </div>
                    @else
                        <span class="text-slate-500 text-xs">-</span>
                    @endif
                </td>
                <td class="p-4 text-right">
                    <form action="{{ route('admin.attendances.destroy', $att->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data absensi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 transition" title="Hapus Data">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="p-8 text-center text-slate-400">Belum ada data absensi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $attendances->links() }}
</div>
@endsection