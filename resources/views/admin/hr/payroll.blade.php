@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30">
                <i class="fa-solid fa-file-invoice-dollar text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Laporan Penggajian (Payroll)</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Rekapitulasi absensi kehadiran, tunjangan harian, potongan telat, dan gaji bersih pegawai.</p>
            </div>
        </div>
        
        <form action="{{ route('admin.hr.payroll.generate') }}" method="POST" onsubmit="return confirm('Generate payroll akan merekap absensi dan menghitung gaji seluruh pegawai di bulan ini. Lanjutkan?')">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-blue-600/20 active:scale-95 transition text-sm">
                <i class="fa-solid fa-calculator text-xs"></i>
                <span>Generate Payroll Bulan Ini</span>
            </button>
        </form>
    </div>

    <!-- Filter Bar & Summary -->
    <div class="bg-white dark:bg-slate-800 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.hr.payroll') }}" method="GET" class="flex flex-wrap items-center gap-2">
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

        <div class="inline-flex items-center gap-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 px-4 py-2 rounded-xl border border-emerald-500/20 text-xs sm:text-sm font-bold font-mono self-start md:self-auto">
            <span>Total Pengeluaran Gaji:</span>
            <span>Rp {{ number_format($payrolls->sum('net_salary'), 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap min-w-[760px]">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Pegawai</th>
                        <th class="p-4 sm:px-6">Presensi</th>
                        <th class="p-4 sm:px-6">Gaji Pokok</th>
                        <th class="p-4 sm:px-6">Uang Makan</th>
                        <th class="p-4 sm:px-6">Bonus Komponen</th>
                        <th class="p-4 sm:px-6">Potongan Telat</th>
                        <th class="p-4 sm:px-6">Take Home Pay</th>
                        <th class="p-4 sm:px-6 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($payrolls as $payroll)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $payroll->user->name ?? 'Unknown' }}</div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">{{ $payroll->user->role ?? '' }}</span>
                        </td>
                        <td class="p-4 sm:px-6 text-xs">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $payroll->present_days }} hari</span>
                            @if($payroll->late_days > 0)
                                <span class="text-red-500 font-semibold block">• Telat: {{ $payroll->late_days }}x</span>
                            @endif
                        </td>
                        <td class="p-4 sm:px-6 font-mono text-xs text-slate-700 dark:text-slate-300">
                            Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}
                        </td>
                        <td class="p-4 sm:px-6 font-mono text-xs text-emerald-600 dark:text-emerald-400">
                            Rp {{ number_format($payroll->total_allowance, 0, ',', '.') }}
                        </td>
                        <td class="p-4 sm:px-6 font-mono text-xs text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($payroll->total_bonus, 0, ',', '.') }}
                        </td>
                        <td class="p-4 sm:px-6 font-mono text-xs text-red-600 dark:text-red-400">
                            (Rp {{ number_format($payroll->total_deduction, 0, ',', '.') }})
                        </td>
                        <td class="p-4 sm:px-6 font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm">
                            Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                        </td>
                        <td class="p-4 sm:px-6 text-center">
                            @if($payroll->status == 'paid')
                                <span class="inline-flex items-center gap-1 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Terbayar
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Draft
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-calculator text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada data payroll di bulan ini. Klik <b>Generate Payroll</b> di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
