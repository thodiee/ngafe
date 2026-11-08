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
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Biaya Operasional (OPEX)</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan pengeluaran harian seperti utilitas, bahan pembersih, sewa, dan perawatan kafe.</p>
            </div>
        </div>
        
        <!-- Filter Form -->
        <form class="flex flex-wrap sm:flex-nowrap items-center gap-2 bg-white dark:bg-slate-800 p-2 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <select name="month" class="bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs sm:text-sm rounded-xl px-3 py-2 outline-none focus:border-brand-500">
                @for($i=1; $i<=12; $i++)
                    <option value="{{ sprintf('%02d', $i) }}" {{ $month == sprintf('%02d', $i) ? 'selected' : '' }}>
                        {{ date('F', mktime(0,0,0,$i,10)) }}
                    </option>
                @endfor
            </select>
            <select name="year" class="bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs sm:text-sm rounded-xl px-3 py-2 outline-none focus:border-brand-500">
                @for($i=date('Y')-2; $i<=date('Y'); $i++)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Filter</span>
            </button>
        </form>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Form (1 Col) -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 sm:p-6 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-base text-slate-900 dark:text-white mb-4 pb-3 border-b border-slate-100 dark:border-slate-700/60">
                    <span class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-plus"></i>
                    </span>
                    <span>Catat Biaya Baru</span>
                </div>
                <form action="{{ route('admin.finance.expenses.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Tanggal Pengeluaran</label>
                        <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Kategori Beban</label>
                        <select name="category" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition">
                            <option value="Gaji & Upah">Gaji & Upah Karyawan</option>
                            <option value="Listrik, Air & Internet">Listrik, Air & Internet</option>
                            <option value="Sewa Tempat">Sewa Tempat & Kebersihan</option>
                            <option value="Marketing & Promosi">Marketing & Promosi</option>
                            <option value="Pemeliharaan Alat">Pemeliharaan Mesin & Alat</option>
                            <option value="Lain-lain">Lain-lain / Tak Terduga</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nominal Biaya (Rp)</label>
                        <input type="number" name="amount" required min="0" placeholder="0" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm font-mono focus:border-brand-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Keterangan / Catatan</label>
                        <textarea name="notes" rows="3" placeholder="Contoh: Pembelian gas LPG 3 tabung dan galon air" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-brand-500 outline-none transition resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-purple-600/20 active:scale-95 transition flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Pengeluaran</span>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Table History (2 Cols) -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="font-bold text-base text-slate-900 dark:text-white">
                            Riwayat Pengeluaran ({{ date('F Y', mktime(0,0,0,$month,10,$year)) }})
                        </h3>
                        <p class="text-xs text-slate-400">Daftar biaya operasional yang dibukukan</p>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 text-xs sm:text-sm font-bold font-mono">
                        <span>Total:</span>
                        <span>Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap min-w-[620px]">
                        <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                            <tr>
                                <th class="p-4 sm:px-5">Tanggal</th>
                                <th class="p-4 sm:px-5">Kategori</th>
                                <th class="p-4 sm:px-5">Keterangan</th>
                                <th class="p-4 sm:px-5 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @forelse($expenses as $exp)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="p-4 sm:px-5 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                                    {{ date('d M Y', strtotime($exp->expense_date)) }}
                                </td>
                                <td class="p-4 sm:px-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                        {{ $exp->category }}
                                    </span>
                                </td>
                                <td class="p-4 sm:px-5 text-slate-600 dark:text-slate-400 text-xs max-w-xs truncate">
                                    {{ $exp->notes ?: '-' }}
                                </td>
                                <td class="p-4 sm:px-5 font-mono font-bold text-red-600 dark:text-red-400 text-right">
                                    Rp {{ number_format($exp->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-12 text-center text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-receipt text-4xl mb-3 opacity-30"></i>
                                    <p>Tidak ada pengeluaran yang tercatat di bulan ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection