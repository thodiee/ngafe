@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Laporan Penggajian (Payroll)</h2>
    <form action="{{ route('admin.hr.payroll.generate') }}" method="POST" class="inline" onsubmit="return confirm('Generate payroll akan merekap absensi dan komponen gaji bulan ini. Lanjutkan?')">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold transition">
            <i class="fa-solid fa-calculator mr-2"></i> Generate Payroll
        </button>
    </form>
</div>

<div class="mb-6 bg-slate-800 p-4 rounded-xl border border-slate-700 shadow flex items-end gap-4">
    <form action="{{ route('admin.hr.payroll') }}" method="GET" class="flex gap-4 items-end w-full">
        <div>
            <label class="block text-sm text-slate-400 mb-1">Bulan</label>
            <select name="month" class="bg-slate-900 border border-slate-700 rounded px-4 py-2 text-white">
                @for($i=1; $i<=12; $i++)
                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm text-slate-400 mb-1">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" class="bg-slate-900 border border-slate-700 rounded px-4 py-2 text-white w-24">
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded text-white font-bold"><i class="fa-solid fa-filter"></i> Filter Periode</button>
    </form>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg overflow-x-auto">
    <table class="w-full text-left whitespace-nowrap">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400 text-sm">
            <tr>
                <th class="p-4">Pegawai</th>
                <th class="p-4">Kehadiran</th>
                <th class="p-4">Gaji Pokok</th>
                <th class="p-4">Uang Makan</th>
                <th class="p-4">Bonus Tambahan</th>
                <th class="p-4">Potongan Total</th>
                <th class="p-4">Take Home Pay</th>
                <th class="p-4">Status</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            @forelse($payrolls as $payroll)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold">{{ $payroll->user->name ?? 'Unknown' }} <br><span class="text-xs font-normal text-slate-400">{{ $payroll->user->role ?? '' }}</span></td>
                <td class="p-4">
                    {{ $payroll->present_days }} hari<br>
                    <span class="text-xs text-red-400">Telat: {{ $payroll->late_days }}x</span>
                </td>
                <td class="p-4">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                <td class="p-4 text-emerald-400">Rp {{ number_format($payroll->total_allowance, 0, ',', '.') }}</td>
                <td class="p-4 text-blue-400">Rp {{ number_format($payroll->total_bonus, 0, ',', '.') }}</td>
                <td class="p-4 text-red-400">Rp {{ number_format($payroll->total_deduction, 0, ',', '.') }}</td>
                <td class="p-4 font-bold text-lg text-emerald-400">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
                <td class="p-4">
                    @if($payroll->status == 'paid')
                        <span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded text-xs font-bold uppercase">Terbayar</span>
                    @else
                        <span class="bg-orange-500/20 text-orange-400 px-2 py-1 rounded text-xs font-bold uppercase">Draft</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="p-4 text-center text-slate-400">Belum ada data payroll untuk bulan ini. Klik Generate Payroll.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
