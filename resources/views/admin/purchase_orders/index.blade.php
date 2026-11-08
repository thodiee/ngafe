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
        <span class="text-slate-800 dark:text-slate-200 font-semibold">Purchase Order</span>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                <i class="fa-solid fa-file-invoice text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Purchase Orders (PO Bahan Baku)</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pengadaan bahan baku resmi ke pemasok/distributor kafe.</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('inventory.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2.5 rounded-xl font-semibold border border-slate-300 dark:border-slate-600 shadow-sm transition-all text-sm">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Kembali ke Stok</span>
            </a>
            <a href="{{ route('admin.purchase_orders.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-emerald-600/20 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Buat Purchase Order</span>
            </a>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap min-w-[700px]">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">No. PO</th>
                        <th class="p-4 sm:px-6">Supplier</th>
                        <th class="p-4 sm:px-6">Tgl. PO</th>
                        <th class="p-4 sm:px-6 text-center">Status</th>
                        <th class="p-4 sm:px-6">Total Nilai Tagihan</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($data as $item)
                    @php
                        $badges = [
                            'draft' => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600',
                            'sent' => 'bg-blue-500/15 text-blue-600 dark:text-blue-400 border-blue-500/30',
                            'partial' => 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/30',
                            'completed' => 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30',
                            'cancelled' => 'bg-red-500/15 text-red-600 dark:text-red-400 border-red-500/30',
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6 font-mono font-bold text-slate-900 dark:text-white">
                            {{ $item->po_number }}
                        </td>
                        <td class="p-4 sm:px-6 font-semibold text-slate-800 dark:text-slate-200 text-xs">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-truck-field text-emerald-500 text-[10px]"></i>
                                {{ $item->supplier->name ?? ('Supplier #' . $item->supplier_id) }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6 text-slate-600 dark:text-slate-400 text-xs">
                            {{ date('d M Y', strtotime($item->po_date)) }}
                        </td>
                        <td class="p-4 sm:px-6 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full font-bold uppercase border {{ $badges[$item->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                            Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.purchase_orders.edit', $item) }}" class="p-2 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 hover:bg-brand-500/20 transition" title="Edit PO">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.purchase_orders.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus PO {{ $item->po_number }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Hapus PO">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-file-circle-question text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada Purchase Order (PO) yang dibuat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
