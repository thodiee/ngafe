@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-700/60">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-brand-500 flex items-center justify-center text-slate-950 font-bold shadow-md shadow-amber-500/20">
                    <i class="fa-solid fa-calendar-check text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Reservasi & Booking Meja</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola jadwal booking meja pelanggan untuk acara, meeting, atau reservasi santai</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-clock text-amber-500"></i> {{ $reservations->count() }} Jadwal Reservasi
            </span>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-600 via-amber-500 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white dark:text-slate-950 font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-amber-500/25 transition text-sm">
                <i class="fa-solid fa-plus"></i> Buat Reservasi
            </button>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[620px] whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700/80 text-xs uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400">
                        <th class="py-4 px-6">Waktu Booking</th>
                        <th class="py-4 px-6">Pelanggan</th>
                        <th class="py-4 px-6">Meja & Area</th>
                        <th class="py-4 px-6">Status Reservasi</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-800 dark:text-slate-200">
                    @forelse($reservations as $res)
                    <tr class="hover:bg-amber-500/[0.04] dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs border border-amber-500/20">
                                    <i class="fa-regular fa-clock"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 dark:text-slate-100 text-sm block">
                                        {{ $res->reservation_time ? $res->reservation_time->format('d M Y') : '-' }}
                                    </span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                        Pukul {{ $res->reservation_time ? $res->reservation_time->format('H:i') : '-' }} WIB
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $res->customer_name }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5 font-mono">
                                <i class="fa-solid fa-phone text-[10px] text-slate-400"></i> {{ $res->customer_phone }}
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 dark:text-slate-100 text-sm">Meja {{ $res->table->table_number ?? 'N/A' }}</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-500/20">
                                    {{ $res->table->area->name ?? 'Area' }}
                                </span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <form action="{{ route('admin.reservations.update', $res->id) }}" method="POST" class="inline-block">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="text-xs font-bold uppercase rounded-lg px-2.5 py-1.5 border cursor-pointer focus:outline-none transition {{ $res->status == 'pending' ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-500/30' : ($res->status == 'confirmed' ? 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-500/30' : ($res->status == 'completed' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/30' : 'bg-rose-500/10 text-rose-700 dark:text-rose-300 border-rose-500/30')) }}">
                                    <option value="pending" {{ $res->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $res->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="completed" {{ $res->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $res->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <form action="{{ route('admin.reservations.destroy', $res->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data reservasi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition" title="Hapus Reservasi">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">Belum ada jadwal reservasi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900 dark:text-white">Buat Reservasi Baru</h3>
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.reservations.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Pilih Meja <span class="text-rose-500">*</span></label>
                <select name="table_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                    @foreach($tables as $table)
                        <option value="{{ $table->id }}">Meja {{ $table->table_number }} ({{ strtoupper($table->area->name ?? 'TANPA AREA') }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Nama Pelanggan <span class="text-rose-500">*</span></label>
                <input type="text" name="customer_name" placeholder="Nama pemesan" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Nomor Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                <input type="text" name="customer_phone" placeholder="08xxxxxxxxxx" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Tanggal & Waktu Reservasi <span class="text-rose-500">*</span></label>
                <input type="datetime-local" name="reservation_time" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>
            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700/60">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-sm transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white font-bold text-sm shadow-md transition">Simpan Reservasi</button>
            </div>
        </form>
    </div>
</div>
@endsection