@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Breadcrumb / Back Navigation -->
    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
        <a href="{{ route('inventory.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition flex items-center gap-1.5">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Dashboard Stok</span>
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 dark:text-slate-200 font-semibold">Transfer Stok</span>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                <i class="fa-solid fa-truck-ramp-box text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Transfer Stok (Gudang ⇄ Dapur)</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan perpindahan fisik bahan baku antara gudang dan dapur operasional.</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('inventory.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2.5 rounded-xl font-semibold border border-slate-300 dark:border-slate-600 shadow-sm transition-all text-sm">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Kembali ke Stok</span>
            </a>
            <a href="{{ route('admin.stock_transfers.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-emerald-600/20 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Buat Transfer Stok</span>
            </a>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap min-w-[680px]">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">No. Transfer</th>
                        <th class="p-4 sm:px-6">Tanggal Transfer</th>
                        <th class="p-4 sm:px-6">Catatan / Keterangan</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($data as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6 font-mono font-bold text-slate-900 dark:text-white">
                            <span class="inline-flex items-center gap-2">
                                <i class="fa-solid fa-arrow-right-arrow-left text-emerald-500 text-xs"></i>
                                {{ $item->transfer_number }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6 text-slate-600 dark:text-slate-400 text-xs">
                            {{ date('d M Y', strtotime($item->transfer_date)) }}
                        </td>
                        <td class="p-4 sm:px-6 text-slate-700 dark:text-slate-300 text-xs max-w-xs truncate">
                            {{ $item->notes ?? '-' }}
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.stock_transfers.edit', $item) }}" class="p-2 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 hover:bg-brand-500/20 transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.stock_transfers.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data transfer {{ $item->transfer_number }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-truck-ramp-box text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada data transfer stok antar lokasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
