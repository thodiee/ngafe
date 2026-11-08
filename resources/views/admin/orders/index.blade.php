@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-700/60">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-brand-500 flex items-center justify-center text-slate-950 font-bold shadow-md shadow-amber-500/20">
                    <i class="fa-solid fa-receipt text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Manajemen Pesanan</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pantau seluruh transaksi pesanan kasir, self-service QR meja, dan riwayat pesanan</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-bag-shopping text-amber-500"></i> {{ $orders->total() }} Total Transaksi
            </span>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 sm:p-5 shadow-sm">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Cari Kode Pesanan</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: ORD-..." class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl pl-8 pr-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Status Pesanan</label>
                <select name="status" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                    <option value="">Semua Status Pesanan</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing (Dapur)</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready (Siap Saji)</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Status Pembayaran</label>
                <select name="payment" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                    <option value="">Semua Pembayaran</option>
                    <option value="unpaid" {{ request('payment') == 'unpaid' ? 'selected' : '' }}>Belum Lunas (Unpaid)</option>
                    <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="refunded" {{ request('payment') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white font-bold py-2 rounded-xl text-sm transition shadow-sm">
                    <i class="fa-solid fa-filter text-xs"></i> Filter
                </button>
                <a href="{{ route('admin.orders.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-sm transition flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Pesanan -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[780px] whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700/80 text-xs uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400">
                        <th class="py-4 px-6">Kode Pesanan</th>
                        <th class="py-4 px-6">Lokasi / Meja</th>
                        <th class="py-4 px-6">Total Tagihan</th>
                        <th class="py-4 px-6">Metode Bayar</th>
                        <th class="py-4 px-6">Status Bayar</th>
                        <th class="py-4 px-6">Status Pesanan</th>
                        <th class="py-4 px-6">Waktu Pesan</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-800 dark:text-slate-200 text-sm">
                    @forelse($orders as $order)
                    <tr class="hover:bg-amber-500/[0.04] dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-4 px-6 font-mono font-bold text-slate-900 dark:text-slate-100">
                            {{ $order->order_reference }}
                        </td>
                        <td class="py-4 px-6">
                            @if($order->table)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-500/20">
                                    <i class="fa-solid fa-chair text-[10px]"></i> Meja {{ $order->table->table_number }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-700 dark:text-purple-300 border border-purple-500/20">
                                    <i class="fa-solid fa-bag-shopping text-[10px]"></i> Takeaway
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-mono font-black text-amber-600 dark:text-brand-400">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 uppercase text-xs font-bold text-slate-600 dark:text-slate-300">
                            {{ str_replace('_', ' ', $order->payment_method ?? '-') }}
                        </td>
                        <td class="py-4 px-6">
                            @if($order->payment_status === 'paid')
                                <span class="inline-flex items-center gap-1 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 px-2.5 py-0.5 rounded-full text-xs font-bold border border-emerald-500/20">
                                    <i class="fa-solid fa-check text-[10px]"></i> Lunas
                                </span>
                            @elseif($order->payment_status === 'refunded')
                                <span class="inline-flex items-center gap-1 bg-blue-500/10 text-blue-700 dark:text-blue-300 px-2.5 py-0.5 rounded-full text-xs font-bold border border-blue-500/20">
                                    Refund
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-rose-500/10 text-rose-700 dark:text-rose-300 px-2.5 py-0.5 rounded-full text-xs font-bold border border-rose-500/20">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Belum Bayar
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if($order->order_status === 'completed')
                                <span class="inline-flex items-center gap-1 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 px-2.5 py-0.5 rounded-full text-xs font-bold border border-emerald-500/20">
                                    Selesai
                                </span>
                            @elseif($order->order_status === 'processing')
                                <span class="inline-flex items-center gap-1 bg-amber-500/10 text-amber-700 dark:text-amber-300 px-2.5 py-0.5 rounded-full text-xs font-bold border border-amber-500/20">
                                    <i class="fa-solid fa-fire-burner text-[10px]"></i> Di Dapur
                                </span>
                            @elseif($order->order_status === 'ready')
                                <span class="inline-flex items-center gap-1 bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 px-2.5 py-0.5 rounded-full text-xs font-bold border border-cyan-500/20">
                                    <i class="fa-solid fa-bell-concierge text-[10px]"></i> Siap Saji
                                </span>
                            @elseif($order->order_status === 'cancelled')
                                <span class="inline-flex items-center gap-1 bg-rose-500/10 text-rose-700 dark:text-rose-300 px-2.5 py-0.5 rounded-full text-xs font-bold border border-rose-500/20">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                    Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 dark:text-slate-400 font-mono">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <button onclick="showDetails({{ json_encode($order->load('items.product', 'items.variants')) }})" class="inline-flex items-center gap-1 bg-slate-100 hover:bg-amber-500 hover:text-white dark:bg-slate-700 dark:hover:bg-amber-500 text-slate-700 dark:text-slate-200 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-xs"></i> Detail
                                </button>
                                @if($order->order_status !== 'cancelled')
                                    <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok bahan baku (BOM) akan dikembalikan secara otomatis.')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white dark:bg-rose-500/10 dark:hover:bg-rose-600 dark:text-rose-300 px-2.5 py-1.5 rounded-lg text-xs font-bold transition">
                                            <i class="fa-solid fa-ban text-xs"></i> Batal
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">Tidak ada data transaksi pesanan ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $orders->appends(request()->query())->links() }}
    </div>
</div>

<!-- Modal Detail Pesanan -->
<div id="detailModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-lg border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden max-h-[90vh] flex flex-col animate-fade-in-up">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-amber-500"></i> Rincian Pesanan
            </h3>
            <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <div class="space-y-4 overflow-y-auto custom-scrollbar p-6 flex-grow">
            <div class="grid grid-cols-2 gap-3 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                <div>
                    <span class="text-slate-400 block mb-0.5">Kode Pesanan</span>
                    <span class="font-bold text-slate-900 dark:text-slate-100 font-mono text-sm" id="detailRef">-</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Nomor Meja</span>
                    <span class="font-bold text-slate-900 dark:text-slate-100 text-sm" id="detailTable">-</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Tipe Pesanan</span>
                    <span class="font-bold text-slate-900 dark:text-slate-100 uppercase" id="detailType">-</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Metode Pembayaran</span>
                    <span class="font-bold text-slate-900 dark:text-slate-100 uppercase" id="detailPayment">-</span>
                </div>
            </div>

            <div>
                <h4 class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-2">Item Menu Yang Dipesan</h4>
                <div class="space-y-2.5" id="detailItemsContainer">
                    <!-- Dinamis terisi oleh JS -->
                </div>
            </div>

            <div class="border-t border-slate-200 dark:border-slate-700 pt-3">
                <div class="flex justify-between items-center font-bold text-base text-slate-900 dark:text-white">
                    <span>Total Pembayaran</span>
                    <span class="text-amber-600 dark:text-brand-400 font-mono text-lg font-black" id="detailTotal">-</span>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-900/40 flex justify-end shrink-0">
            <button type="button" onclick="closeDetailModal()" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold text-sm transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function showDetails(order) {
        document.getElementById('detailRef').innerText = order.order_reference;
        document.getElementById('detailTable').innerText = order.table ? 'Meja ' + order.table.table_number : 'Takeaway';
        document.getElementById('detailType').innerText = order.order_type;
        document.getElementById('detailPayment').innerText = order.payment_method ? order.payment_method.replace('_', ' ') : '-';
        document.getElementById('detailTotal').innerText = 'Rp ' + Number(order.total_amount).toLocaleString('id-ID');

        const container = document.getElementById('detailItemsContainer');
        container.innerHTML = '';

        order.items.forEach(item => {
            let variantHtml = '';
            if (item.variants && item.variants.length > 0) {
                let varList = item.variants.map(v => `${v.variant_name}: ${v.option_name} (+Rp ${Number(v.price_adjustment).toLocaleString('id-ID')})`).join(', ');
                variantHtml = `<div class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic"><i class="fa-solid fa-sliders mr-1 text-amber-500"></i>${varList}</div>`;
            }

            let notesHtml = '';
            if (item.notes) {
                notesHtml = `<div class="text-xs text-amber-600 dark:text-amber-400 mt-1"><i class="fa-solid fa-comment-dots mr-1"></i>Catatan: ${item.notes}</div>`;
            }

            const itemHtml = `
                <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700 p-3 rounded-xl flex justify-between items-start gap-4">
                    <div>
                        <div class="font-bold text-slate-900 dark:text-slate-100 text-sm">${item.product ? item.product.name : 'Produk #' + item.product_id}</div>
                        ${variantHtml}
                        ${notesHtml}
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-mono">${item.quantity}x @ Rp ${Number(item.price).toLocaleString('id-ID')}</div>
                        <div class="font-bold font-mono text-amber-600 dark:text-brand-400 text-sm mt-0.5">Rp ${Number((Number(item.price) + item.variants.reduce((acc, v) => acc + Number(v.price_adjustment), 0)) * item.quantity).toLocaleString('id-ID')}</div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        });

        document.getElementById('detailModal').classList.remove('hidden');
    }

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
