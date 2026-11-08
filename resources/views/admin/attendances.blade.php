@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30">
                    <i class="fa-solid fa-fingerprint text-sm"></i>
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Manajemen Absensi Pegawai</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Pantau presensi harian, verifikasi wajah selfie, dan data jam kerja staf.</p>
        </div>
        
        <!-- Bulk Clean Photo Proofs -->
        <form action="{{ route('admin.attendances.bulk_destroy_proof') }}" method="POST" class="flex flex-wrap sm:flex-nowrap items-center gap-2 bg-white dark:bg-slate-800 p-2 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm" onsubmit="return confirm('Yakin ingin menghapus foto absensi lama? Data rekapan waktu kehadiran tetap tersimpan.');">
            @csrf
            <select name="months" class="bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs sm:text-sm rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500 outline-none flex-grow sm:flex-grow-0">
                <option value="1">Foto > 1 Bulan</option>
                <option value="2">Foto > 2 Bulan</option>
                <option value="3">Foto > 3 Bulan</option>
                <option value="6">Foto > 6 Bulan</option>
                <option value="12">Foto > 1 Tahun</option>
            </select>
            <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center justify-center gap-2 shadow-sm shrink-0">
                <i class="fa-solid fa-broom text-xs"></i>
                <span>Bersihkan Foto</span>
            </button>
        </form>
    </div>

    {{-- VALIDASI WAJAH PENDING --}}
    @if($pendingFaceUsers->count() > 0)
    <div class="bg-gradient-to-r from-orange-500/10 via-amber-500/10 to-transparent border border-orange-500/30 rounded-2xl p-4 sm:p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-orange-600 dark:text-orange-400 flex items-center gap-2 text-sm sm:text-base">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                </span>
                Menunggu Validasi Foto Wajah ({{ $pendingFaceUsers->count() }})
            </h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($pendingFaceUsers as $u)
            <div class="bg-white dark:bg-slate-800 rounded-xl p-3.5 flex items-center justify-between border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-500/15 rounded-xl flex items-center justify-center font-bold text-orange-600 dark:text-orange-400 border border-orange-500/30 shrink-0">
                        {{ strtoupper(substr($u->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white">{{ $u->name }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 capitalize">{{ $u->role }}</div>
                    </div>
                </div>
                <form action="{{ route('admin.users.verify_face', $u->id) }}" method="POST" onsubmit="return confirm('Verifikasi wajah {{ $u->name }}?')">
                    @csrf
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm">
                        <i class="fa-solid fa-check"></i>
                        <span>Verifikasi</span>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- RIWAYAT ABSENSI TABLE --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Tanggal</th>
                        <th class="p-4 sm:px-6">Pegawai</th>
                        <th class="p-4 sm:px-6">Clock In</th>
                        <th class="p-4 sm:px-6">Clock Out</th>
                        <th class="p-4 sm:px-6">Status Kehadiran</th>
                        <th class="p-4 sm:px-6">Titik Lokasi</th>
                        <th class="p-4 sm:px-6">Bukti Foto</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6 font-semibold text-slate-800 dark:text-slate-200">
                            {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}
                        </td>
                        <td class="p-4 sm:px-6">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $att->user->name ?? 'Unknown' }}</div>
                            <div class="text-xs text-slate-400 capitalize">{{ $att->user->role ?? '-' }}</div>
                        </td>
                        <td class="p-4 sm:px-6">
                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20 text-xs">
                                {{ $att->clock_in ?? '-' }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6">
                            <span class="font-mono font-bold text-orange-600 dark:text-orange-400 bg-orange-500/10 px-2.5 py-1 rounded-lg border border-orange-500/20 text-xs">
                                {{ $att->clock_out ?? '-' }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6">
                            @if($att->status == 'ontime')
                                <span class="inline-flex items-center gap-1.5 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-bold border border-emerald-500/30">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Tepat Waktu
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 bg-amber-500/15 text-amber-600 dark:text-amber-400 px-3 py-1 rounded-full text-xs font-bold border border-amber-500/30">
                                    <i class="fa-solid fa-clock-rotate-left text-[10px]"></i> Terlambat
                                </span>
                            @endif
                        </td>
                        <td class="p-4 sm:px-6">
                            @if($att->latitude && $att->longitude)
                                <a href="https://www.google.com/maps?q={{ $att->latitude }},{{ $att->longitude }}" target="_blank" class="inline-flex items-center gap-1.5 text-blue-600 dark:text-blue-400 hover:underline text-xs bg-blue-500/10 px-2.5 py-1 rounded-lg border border-blue-500/20">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>Buka Maps</span>
                                </a>
                            @else
                                <span class="text-slate-400 text-xs italic">Tanpa GPS</span>
                            @endif
                        </td>
                        <td class="p-4 sm:px-6">
                            @if($att->photo_proof)
                                <div class="flex items-center gap-2">
                                    <button onclick="document.getElementById('proof-{{ $att->id }}').classList.remove('hidden'); document.getElementById('proof-{{ $att->id }}').classList.add('flex')" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-medium transition">
                                        <i class="fa-solid fa-image text-brand-500"></i>
                                        <span>Lihat Selfie</span>
                                    </button>
                                    <form action="{{ route('admin.attendances.destroy_proof', $att->id) }}" method="POST" onsubmit="return confirm('Hapus foto selfie ini?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1 text-red-500 hover:text-red-400 text-xs transition" title="Hapus Foto">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Photo Proof Modal -->
                                <div id="proof-{{ $att->id }}" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                                    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-sm w-full relative shadow-2xl">
                                        <button onclick="document.getElementById('proof-{{ $att->id }}').classList.add('hidden'); document.getElementById('proof-{{ $att->id }}').classList.remove('flex')" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                        <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-3">Foto Bukti Kehadiran</h4>
                                        <img src="{{ $att->photo_proof }}" class="w-full h-72 object-cover rounded-2xl border border-slate-200 dark:border-slate-800 shadow" alt="Foto Bukti">
                                        <div class="mt-3 text-center">
                                            <div class="font-semibold text-xs text-slate-900 dark:text-white">{{ $att->user->name ?? 'Pegawai' }}</div>
                                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $att->date }} • Masuk: {{ $att->clock_in ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-slate-400 text-xs italic">Tanpa Foto</span>
                            @endif
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            <form action="{{ route('admin.attendances.destroy', $att->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus rekam absensi ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Hapus Record">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-clipboard-user text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada riwayat absensi yang tercatat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $attendances->links() }}
    </div>
</div>
@endsection