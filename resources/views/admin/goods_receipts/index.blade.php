@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                <i class="fa-solid fa-boxes-packing text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Penerimaan Barang (Goods Receipt)</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Verifikasi fisik dan pencatatan kedatangan stok bahan baku dari supplier ke gudang.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.goods_receipts.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-emerald-600/20 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Catat Penerimaan Barang</span>
            </a>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="p-4 sm:px-6">No. Penerimaan (GR)</th>
                        <th class="p-4 sm:px-6">Ref. Purchase Order</th>
                        <th class="p-4 sm:px-6">Supplier</th>
                        <th class="p-4 sm:px-6">Tgl. Diterima</th>
                        <th class="p-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($data as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 sm:px-6 font-mono font-bold text-slate-900 dark:text-white">
                            {{ $item->gr_number }}
                        </td>
                        <td class="p-4 sm:px-6 font-mono text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                            {{ $item->purchaseOrder->po_number ?? ($item->purchase_order_id ? 'PO #'.$item->purchase_order_id : '-') }}
                        </td>
                        <td class="p-4 sm:px-6 font-semibold text-slate-800 dark:text-slate-200 text-xs">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-truck-field text-emerald-500 text-[10px]"></i>
                                {{ $item->supplier->name ?? ($item->purchaseOrder->supplier->name ?? '-') }}
                            </span>
                        </td>
                        <td class="p-4 sm:px-6 text-slate-600 dark:text-slate-400 text-xs">
                            {{ date('d M Y', strtotime($item->receipt_date)) }}
                        </td>
                        <td class="p-4 sm:px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.goods_receipts.edit', $item) }}" class="p-2 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 hover:bg-brand-500/20 transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.goods_receipts.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penerimaan barang {{ $item->gr_number }}?')">
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
                        <td colspan="5" class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-boxes-packing text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada rekaman penerimaan barang (Goods Receipt).</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
