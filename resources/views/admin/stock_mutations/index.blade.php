@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                <i class="fa-solid fa-right-left text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Jurnal Mutasi Stok Bahan</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Catatan audit log setiap penambahan dan pengurangan saldo bahan baku kafe.</p>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">Waktu Mutasi</th>
                        <th class="p-4 sm:px-6">Nama Bahan Baku</th>
                        <th class="p-4 sm:px-6">Lokasi</th>
                        <th class="p-4 sm:px-6 text-center">Tipe Alur</th>
                        <th class="p-4 sm:px-6">Kuantitas</th>
                        <th class="p-4 sm:px-6">Dokumen Referensi</th>
                        <th class="p-4 sm:px-6">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($data as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6 text-xs text-slate-500 dark:text-slate-400 font-mono">
                            {{ $item->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="p-4 sm:px-6 font-bold text-slate-900 dark:text-white">
                            {{ $item->ingredient->name ?? ('Bahan #' . $item->ingredient_id) }}
                        </td>
                        <td class="p-4 sm:px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item->location == 'gudang' ? 'bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/30' : 'bg-purple-500/15 text-purple-600 dark:text-purple-400 border border-purple-500/30' }}">
                                {{ ucfirst($item->location) }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6 text-center">
                            @if($item->type == 'in')
                                <span class="inline-flex items-center gap-1 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 px-2.5 py-1 rounded-full text-xs font-bold uppercase">
                                    <i class="fa-solid fa-arrow-down text-[10px]"></i> Masuk
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-red-500/15 text-red-600 dark:text-red-400 border border-red-500/30 px-2.5 py-1 rounded-full text-xs font-bold uppercase">
                                    <i class="fa-solid fa-arrow-up text-[10px]"></i> Keluar
                                </span>
                            @endif
                        </td>
                        <td class="p-4 sm:px-6 font-mono font-bold text-xs {{ $item->type == 'in' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $item->type == 'in' ? '+' : '-' }}{{ $item->quantity }} {{ $item->ingredient->unit ?? '' }}
                        </td>
                        <td class="p-4 sm:px-6 text-xs text-slate-600 dark:text-slate-300 font-mono">
                            {{ $item->reference_type }} {{ $item->reference_id ? '#'.$item->reference_id : '' }}
                        </td>
                        <td class="p-4 sm:px-6 text-xs text-slate-500 dark:text-slate-400 max-w-xs truncate">
                            {{ $item->notes ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-clipboard-list text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada data jurnal mutasi stok bahan baku.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
