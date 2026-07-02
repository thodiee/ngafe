@extends('admin.layout')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-orange-500">
            <i class="fa-solid fa-receipt mr-3 text-amber-500"></i>Manajemen Pesanan
        </h2>
        <p class="text-slate-400 mt-1">Kelola, tinjau detail, dan lakukan pembatalan pesanan pelanggan.</p>
    </div>
</div>

{{-- Filter & Pencarian --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 mb-6">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-slate-400 mb-1">Cari Kode Pesanan</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: ORD-..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm">
        </div>
        <div>
            <label class="block text-xs text-slate-400 mb-1">Status Pesanan</label>
            <select name="status" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-400 mb-1">Status Pembayaran</label>
            <select name="payment" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm">
                <option value="">Semua Pembayaran</option>
                <option value="unpaid" {{ request('payment') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="refunded" {{ request('payment') == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-amber-600 hover:bg-amber-500 px-4 py-2 rounded-lg text-white font-bold text-sm transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Cari
            </button>
            <a href="{{ route('admin.orders.index') }}" class="bg-slate-700 hover:bg-slate-600 px-4 py-2 rounded-lg text-white text-sm transition">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- Tabel Pesanan --}}
<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <div class="overflow-x-auto">
        <table class="w-full text-left font-sans">
            <thead class="bg-slate-900 border-b border-slate-700 text-slate-400 text-sm">
                <tr>
                    <th class="p-4">Kode / Pelanggan</th>
                    <th class="p-4">Meja</th>
                    <th class="p-4">Tipe</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Metode Bayar</th>
                    <th class="p-4">Status Bayar</th>
                    <th class="p-4">Status Pesanan</th>
                    <th class="p-4">Waktu Pesan</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($orders as $order)
                <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                    <td class="p-4 font-bold text-white">
                        {{ $order->order_reference }}
                    </td>
                    <td class="p-4">
                        {{ $order->table->table_number ?? 'N/A' }}
                    </td>
                    <td class="p-4 uppercase text-xs font-semibold">
                        {{ $order->order_type }}
                    </td>
                    <td class="p-4 font-bold text-emerald-400">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="p-4 uppercase text-xs">
                        {{ str_replace('_', ' ', $order->payment_method ?? '-') }}
                    </td>
                    <td class="p-4">
                        @if($order->payment_status === 'paid')
                            <span class="bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded text-xs font-bold uppercase border border-emerald-500/20">Paid</span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded text-xs font-bold uppercase border border-blue-500/20">Refunded</span>
                        @else
                            <span class="bg-rose-500/20 text-rose-400 px-2 py-0.5 rounded text-xs font-bold uppercase border border-rose-500/20">Unpaid</span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($order->order_status === 'completed')
                            <span class="bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded text-xs font-bold uppercase border border-emerald-500/20">Completed</span>
                        @elseif($order->order_status === 'processing')
                            <span class="bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded text-xs font-bold uppercase border border-blue-500/20">Processing</span>
                        @elseif($order->order_status === 'ready')
                            <span class="bg-amber-500/20 text-amber-400 px-2 py-0.5 rounded text-xs font-bold uppercase border border-amber-500/20">Ready</span>
                        @elseif($order->order_status === 'cancelled')
                            <span class="bg-rose-500/20 text-rose-400 px-2 py-0.5 rounded text-xs font-bold uppercase border border-rose-500/20">Cancelled</span>
                        @else
                            <span class="bg-slate-700 text-slate-300 px-2 py-0.5 rounded text-xs font-bold uppercase border border-slate-600/30">Pending</span>
                        @endif
                    </td>
                    <td class="p-4 text-xs text-slate-400">
                        {{ $order->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="p-4 text-right flex items-center justify-end gap-2">
                        <button onclick="showDetails({{ json_encode($order->load('items.product', 'items.variants')) }})" class="bg-blue-600/20 text-blue-400 border border-blue-500/30 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                            <i class="fa-solid fa-eye mr-1"></i> Detail
                        </button>
                        @if($order->order_status !== 'cancelled')
                            <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok bahan baku (BOM) akan dikembalikan secara otomatis.')">
                                @csrf
                                <button type="submit" class="bg-rose-600/20 text-rose-400 border border-rose-500/30 hover:bg-rose-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-ban mr-1"></i> Batal
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="p-8 text-center text-slate-500">Tidak ada data pesanan ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $orders->appends(request()->query())->links() }}
</div>

<!-- Modal Detail Pesanan -->
<div id="detailModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-lg border border-slate-700 shadow-2xl overflow-hidden max-h-[85vh] flex flex-col">
        <div class="flex justify-between items-center pb-3 border-b border-slate-700 mb-4 shrink-0">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fa-solid fa-circle-info mr-2 text-brand-500"></i>Detail Pesanan
            </h3>
            <button onclick="closeDetailModal()" class="text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <div class="space-y-4 overflow-y-auto custom-scrollbar pr-1 flex-grow">
            <div class="grid grid-cols-2 gap-4 bg-slate-900/50 p-4 rounded-xl border border-slate-700 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Kode Pesanan</span>
                    <span class="font-bold text-white" id="detailRef">-</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Nomor Meja</span>
                    <span class="font-bold text-white" id="detailTable">-</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Tipe Pesanan</span>
                    <span class="font-bold text-white uppercase" id="detailType">-</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Metode Pembayaran</span>
                    <span class="font-bold text-white uppercase" id="detailPayment">-</span>
                </div>
            </div>

            <div>
                <h4 class="text-xs text-slate-400 uppercase font-semibold tracking-wider mb-2">Item Pesanan</h4>
                <div class="space-y-2" id="detailItemsContainer">
                    <!-- Dinamis terisi oleh JS -->
                </div>
            </div>

            <div class="border-t border-slate-700 pt-3">
                <div class="flex justify-between font-bold text-lg text-white">
                    <span>Total Pembayaran</span>
                    <span class="text-emerald-400" id="detailTotal">-</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-700 mt-4 shrink-0">
            <button type="button" onclick="closeDetailModal()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded text-white font-medium transition text-sm">Close</button>
        </div>
    </div>
</div>

<script>
    function showDetails(order) {
        document.getElementById('detailRef').innerText = order.order_reference;
        document.getElementById('detailTable').innerText = order.table ? order.table.table_number : 'Takeaway';
        document.getElementById('detailType').innerText = order.order_type;
        document.getElementById('detailPayment').innerText = order.payment_method ? order.payment_method.replace('_', ' ') : '-';
        document.getElementById('detailTotal').innerText = 'Rp ' + Number(order.total_amount).toLocaleString('id-ID');

        const container = document.getElementById('detailItemsContainer');
        container.innerHTML = '';

        order.items.forEach(item => {
            let variantHtml = '';
            if (item.variants && item.variants.length > 0) {
                let varList = item.variants.map(v => `${v.variant_name}: ${v.option_name} (+Rp ${Number(v.price_adjustment).toLocaleString('id-ID')})`).join(', ');
                variantHtml = `<div class="text-xs text-slate-400 mt-0.5 italic"><i class="fa-solid fa-list-check mr-1"></i>${varList}</div>`;
            }

            let notesHtml = '';
            if (item.notes) {
                notesHtml = `<div class="text-xs text-amber-400/80 mt-0.5"><i class="fa-solid fa-comment-dots mr-1"></i>Catatan: ${item.notes}</div>`;
            }

            const itemHtml = `
                <div class="bg-slate-900 border border-slate-800 p-3 rounded-lg flex justify-between items-start gap-4">
                    <div>
                        <div class="font-bold text-white text-sm">${item.product ? item.product.name : 'Produk #' + item.product_id}</div>
                        ${variantHtml}
                        ${notesHtml}
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-400">${item.quantity} x Rp ${Number(item.price).toLocaleString('id-ID')}</div>
                        <div class="font-bold text-emerald-400 text-sm mt-0.5">Rp ${Number((Number(item.price) + item.variants.reduce((acc, v) => acc + Number(v.price_adjustment), 0)) * item.quantity).toLocaleString('id-ID')}</div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        });

        document.getElementById('detailModal').classList.remove('hidden');
    }

    // Support Turbo load
    document.addEventListener("turbo:load", function() {
        const modal = document.getElementById('detailModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    });

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
</script>
@endsection
