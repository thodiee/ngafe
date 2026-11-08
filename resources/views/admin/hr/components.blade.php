@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30">
                <i class="fa-solid fa-money-bill-transfer text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Komponen Gaji Tambahan</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Atur bonus kinerja, insentif lembur, atau potongan manual untuk slip gaji pegawai.</p>
            </div>
        </div>
        <div>
            <button onclick="document.getElementById('addModal').classList.remove('hidden'); document.getElementById('addModal').classList.add('flex')" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-purple-600/20 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Komponen</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-800 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form action="{{ route('admin.hr.components') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <select name="month" class="bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs sm:text-sm rounded-xl px-3 py-2 outline-none focus:border-brand-500">
                @for($i=1; $i<=12; $i++)
                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                </option>
                @endfor
            </select>
            <input type="number" name="year" value="{{ $year }}" class="bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs sm:text-sm rounded-xl px-3 py-2 w-24 outline-none focus:border-brand-500">
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Filter</span>
            </button>
        </form>

        <span class="text-xs text-slate-400">Periode: {{ date('F Y', mktime(0, 0, 0, (int)$month, 10, (int)$year)) }}</span>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap min-w-[640px]">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Pegawai</th>
                        <th class="p-4 sm:px-6">Jenis</th>
                        <th class="p-4 sm:px-6">Nama / Keterangan</th>
                        <th class="p-4 sm:px-6">Nominal</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($components as $comp)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6 font-bold text-slate-900 dark:text-white">
                            {{ $comp->user->name ?? 'Unknown' }}
                            <span class="text-xs font-normal text-slate-400 block uppercase">{{ $comp->user->role ?? '' }}</span>
                        </td>
                        <td class="p-4 sm:px-6">
                            @if($comp->type == 'bonus')
                                <span class="inline-flex items-center gap-1 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 px-2.5 py-1 rounded-full text-xs font-bold uppercase">
                                    <i class="fa-solid fa-plus text-[10px]"></i> Bonus
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-red-500/15 text-red-600 dark:text-red-400 border border-red-500/30 px-2.5 py-1 rounded-full text-xs font-bold uppercase">
                                    <i class="fa-solid fa-minus text-[10px]"></i> Potongan
                                </span>
                            @endif
                        </td>
                        <td class="p-4 sm:px-6 text-slate-800 dark:text-slate-200 text-xs font-medium">
                            {{ $comp->name }}
                        </td>
                        <td class="p-4 sm:px-6 font-mono font-bold text-xs {{ $comp->type == 'bonus' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $comp->type == 'bonus' ? '+' : '-' }} Rp {{ number_format($comp->amount, 0, ',', '.') }}
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            <form action="{{ route('admin.hr.components.destroy', $comp->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus komponen gaji ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-coins text-4xl mb-3 opacity-30"></i>
                            <p>Tidak ada komponen gaji tambahan di periode ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Komponen -->
<div id="addModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-7 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Komponen Gaji</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden'); document.getElementById('addModal').classList.remove('flex')" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.hr.components.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            <input type="hidden" name="applied_month" value="{{ $month }}">
            <input type="hidden" name="applied_year" value="{{ $year }}">
            
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Pilih Pegawai</label>
                <select name="user_id" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-purple-500 outline-none transition" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ strtoupper($user->role) }})</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Jenis Komponen</label>
                <select name="type" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-purple-500 outline-none transition" required>
                    <option value="bonus">Bonus (Penambah Gaji Bersih)</option>
                    <option value="deduction">Potongan (Pengurang Gaji Bersih)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nama / Keterangan</label>
                <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-purple-500 outline-none transition" placeholder="Contoh: Bonus Target Bulanan, Kerusakan Alat" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nominal (Rp)</label>
                <input type="number" name="amount" min="1" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm font-mono focus:border-purple-500 outline-none transition" placeholder="0" required>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden'); document.getElementById('addModal').classList.remove('flex')" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition text-sm">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold shadow-lg shadow-purple-600/20 active:scale-95 transition text-sm">
                    Simpan Komponen
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
