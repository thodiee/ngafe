@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Komponen Gaji (Bonus & Potongan)</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Komponen</button>
</div>

<div class="mb-6 bg-slate-800 p-4 rounded-xl border border-slate-700 shadow flex items-end gap-4">
    <form action="{{ route('admin.hr.components') }}" method="GET" class="flex gap-4 items-end w-full">
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
        <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded text-white font-bold"><i class="fa-solid fa-filter"></i> Filter</button>
    </form>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Pegawai</th>
                <th class="p-4">Jenis</th>
                <th class="p-4">Nama / Kriteria</th>
                <th class="p-4">Nominal</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($components as $comp)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold">{{ $comp->user->name ?? 'Unknown' }}</td>
                <td class="p-4">
                    @if($comp->type == 'bonus')
                        <span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded text-xs font-bold uppercase">Bonus</span>
                    @else
                        <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs font-bold uppercase">Potongan</span>
                    @endif
                </td>
                <td class="p-4">{{ $comp->name }}</td>
                <td class="p-4 text-{{ $comp->type == 'bonus' ? 'emerald' : 'red' }}-400 font-bold">
                    {{ $comp->type == 'bonus' ? '+' : '-' }} Rp {{ number_format($comp->amount, 0, ',', '.') }}
                </td>
                <td class="p-4 text-right">
                    <form action="{{ route('admin.hr.components.destroy', $comp->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus komponen ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 ml-2"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-4 text-center text-slate-400">Tidak ada komponen gaji tambahan di periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Tambah Komponen Gaji</h3>
        <form action="{{ route('admin.hr.components.store') }}" method="POST">
            @csrf
            <input type="hidden" name="applied_month" value="{{ $month }}">
            <input type="hidden" name="applied_year" value="{{ $year }}">
            
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Pegawai</label>
                <select name="user_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->role }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Jenis</label>
                <select name="type" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    <option value="bonus">Bonus (Penambah)</option>
                    <option value="deduction">Potongan (Pengurang)</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama / Kriteria (Contoh: Target Sales, Denda Barang)</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>

            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Nominal (Rp)</label>
                <input type="number" name="amount" min="1" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
