@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-white"><i class="fa-solid fa-money-bill-transfer text-purple-400 mr-2"></i> Pengeluaran (OPEX)</h2>
    <form class="flex gap-2">
        <select name="month" class="bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2">
            @for($i=1; $i<=12; $i++)
                <option value="{{ sprintf('%02d', $i) }}" {{ $month == sprintf('%02d', $i) ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,10)) }}</option>
            @endfor
        </select>
        <select name="year" class="bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2">
            @for($i=date('Y')-2; $i<=date('Y'); $i++)
                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg transition"><i class="fa-solid fa-filter"></i> Filter</button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 shadow-xl sticky top-24">
            <h3 class="text-xl font-bold text-white mb-6"><i class="fa-solid fa-plus-circle text-emerald-400 mr-2"></i> Catat Pengeluaran</h3>
            <form action="{{ route('admin.finance.expenses.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-slate-400 mb-1">Tanggal</label>
                    <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:border-brand-500 outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-slate-400 mb-1">Kategori</label>
                    <select name="category" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:border-brand-500 outline-none">
                        <option value="Gaji & Upah">Gaji & Upah</option>
                        <option value="Listrik, Air & Internet">Listrik, Air & Internet</option>
                        <option value="Sewa Tempat">Sewa Tempat</option>
                        <option value="Marketing & Promosi">Marketing & Promosi</option>
                        <option value="Pemeliharaan Alat">Pemeliharaan Alat</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-slate-400 mb-1">Nominal (Rp)</label>
                    <input type="number" name="amount" required min="0" placeholder="0" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:border-brand-500 outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-sm text-slate-400 mb-1">Catatan</label>
                    <textarea name="notes" rows="3" placeholder="Opsional" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:border-brand-500 outline-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-500 text-white font-bold py-3 rounded-lg transition-colors"><i class="fa-solid fa-save mr-2"></i> Simpan Data</button>
            </form>
        </div>
    </div>
    
    <div class="lg:col-span-2">
        <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden shadow-xl">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-lg text-white">Riwayat Pengeluaran ({{ date('F Y', mktime(0,0,0,$month,10,$year)) }})</h3>
                <div class="text-xl font-bold text-red-400">Total: Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-900/50 text-slate-400 text-sm border-b border-slate-700">
                            <th class="p-4 font-medium">Tanggal</th>
                            <th class="p-4 font-medium">Kategori</th>
                            <th class="p-4 font-medium">Keterangan</th>
                            <th class="p-4 font-medium text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                        <tr class="border-b border-slate-700/50 hover:bg-slate-700/20 transition">
                            <td class="p-4 text-slate-300">{{ date('d M Y', strtotime($exp->expense_date)) }}</td>
                            <td class="p-4">
                                <span class="bg-slate-700 text-xs px-2 py-1 rounded text-slate-300">{{ $exp->category }}</span>
                            </td>
                            <td class="p-4 text-slate-400 text-sm">{{ $exp->notes ?: '-' }}</td>
                            <td class="p-4 font-bold text-red-400 text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500">Tidak ada pengeluaran di bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection