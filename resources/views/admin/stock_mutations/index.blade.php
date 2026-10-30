@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Stock Mutations (Jurnal Stok)</h2>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Tanggal</th>
                <th class="p-4">Ingredient ID</th>
                <th class="p-4">Lokasi</th>
                <th class="p-4">Tipe</th>
                <th class="p-4">Kuantitas</th>
                <th class="p-4">Referensi</th>
                <th class="p-4">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4">{{ $item->created_at->format('Y-m-d H:i') }}</td>
                <td class="p-4 font-bold">{{ $item->ingredient->name ?? $item->ingredient_id }}</td>
                <td class="p-4">{{ ucfirst($item->location) }}</td>
                <td class="p-4">
                    @if($item->type == 'in')
                        <span class="text-emerald-400 font-bold bg-emerald-400/10 px-2 py-1 rounded">Masuk</span>
                    @else
                        <span class="text-red-400 font-bold bg-red-400/10 px-2 py-1 rounded">Keluar</span>
                    @endif
                </td>
                <td class="p-4 font-mono">{{ $item->quantity }}</td>
                <td class="p-4">{{ $item->reference_type }} {{ $item->reference_id ? '#'.$item->reference_id : '' }}</td>
                <td class="p-4 text-sm text-slate-300">{{ $item->notes }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="p-4 text-center text-slate-400">Belum ada data mutasi stok.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
