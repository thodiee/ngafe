@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Penerimaan Barang (Goods Receipt)</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Verifikasi dan pencatatan kedatangan fisik bahan baku dari pemasok.</p>
    </div>
    <a href="{{ route('admin.goods_receipts.create') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg transition inline-flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Catat Penerimaan
    </a>
</div>
<div class="bg-slate-800 rounded-xl overflow-hidden border border-slate-700 shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400 text-sm">
            <tr>
                <th class="py-3.5 px-4 font-semibold">No. GR</th>
                <th class="py-3.5 px-4 font-semibold">Ref. Purchase Order</th>
                <th class="py-3.5 px-4 font-semibold">Supplier</th>
                <th class="py-3.5 px-4 font-semibold">Tgl. Penerimaan</th>
                <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700/50">
            @forelse($data as $item)
            <tr class="hover:bg-slate-700/30 transition">
                <td class="py-3.5 px-4 font-mono font-bold text-white">{{ $item->gr_number }}</td>
                <td class="py-3.5 px-4 font-mono text-emerald-400 text-sm">
                    {{ $item->purchaseOrder->po_number ?? ($item->purchase_order_id ? 'PO #'.$item->purchase_order_id : '-') }}
                </td>
                <td class="py-3.5 px-4 text-slate-300">
                    <span class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-truck-field text-emerald-400"></i>
                        {{ $item->supplier->name ?? ($item->purchaseOrder->supplier->name ?? '-') }}
                    </span>
                </td>
                <td class="py-3.5 px-4 text-slate-400 text-sm">{{ $item->receipt_date }}</td>
                <td class="py-3.5 px-4 text-right space-x-2">
                    <a href="{{ route('admin.goods_receipts.edit', $item) }}" class="inline-flex items-center gap-1 text-sm bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 px-3 py-1.5 rounded-lg transition">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                    <form action="{{ route('admin.goods_receipts.destroy', $item) }}" method="POST" class="inline-block">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 text-sm bg-red-500/10 hover:bg-red-500/20 text-red-400 px-3 py-1.5 rounded-lg transition" onclick="return confirm('Yakin ingin menghapus data penerimaan ini?')">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data penerimaan barang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
