@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-700/60">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-brand-500 flex items-center justify-center text-slate-950 font-bold shadow-md shadow-amber-500/20">
                    <i class="fa-solid fa-box-open text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Paket Menu & Bundling</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola paket bundling hemat untuk memikat pelanggan dan meningkatkan penjualan</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-cubes text-amber-500"></i> {{ $packages->count() }} Paket Terdaftar
            </span>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-600 via-amber-500 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white dark:text-slate-950 font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 transition-all duration-200 text-sm">
                <i class="fa-solid fa-plus"></i> Buat Paket Baru
            </button>
        </div>
    </div>

    @if($packages->count() == 0)
    <!-- Empty State -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-12 text-center shadow-sm">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl border border-amber-500/20">
            <i class="fa-solid fa-box-open"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Belum Ada Paket Menu</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">Buat paket bundling pertama Anda untuk menggabungkan makanan, minuman, dan snack dengan harga hemat spesial.</p>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition text-sm">
            <i class="fa-solid fa-plus"></i> Buat Paket Sekarang
        </button>
    </div>
    @else
    <!-- Packages Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @foreach($packages as $pkg)
        @php
            $normalTotal = $pkg->packageItems->sum(function($item) {
                return ($item->product->price ?? 0) * $item->quantity;
            });
            $saving = $normalTotal - $pkg->price;
        @endphp
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden flex flex-col">
            
            <!-- Card Header -->
            <div class="p-5 sm:p-6 bg-slate-50/70 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-700/80">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1.5 flex-grow">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-gradient-to-r from-amber-500/20 to-brand-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                <i class="fa-solid fa-box-open text-[10px] text-amber-500"></i> Paket Bundling
                            </span>
                            @if($pkg->category)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-500/10 text-purple-700 dark:text-purple-300 border border-purple-500/20">
                                {{ $pkg->category->name }}
                            </span>
                            @endif
                        </div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ $pkg->name }}</h3>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button onclick="openEditModal({{ $pkg->id }}, '{{ addslashes($pkg->name) }}', {{ $pkg->category_id }}, {{ $pkg->price }})" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-slate-700 transition" title="Edit Paket">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <form action="{{ route('admin.products.destroy', $pkg->id) }}" method="POST" onsubmit="return confirm('Hapus paket \'{{ $pkg->name }}\' secara permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition" title="Hapus Paket">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Price & Value Comparison -->
                <div class="mt-4 pt-4 border-t border-slate-200/80 dark:border-slate-700/60 flex flex-wrap items-baseline justify-between gap-2">
                    <div>
                        <span class="text-xs uppercase font-bold tracking-wider text-slate-400 dark:text-slate-500 block mb-0.5">Harga Jual Paket</span>
                        <div class="text-2xl font-black font-mono text-amber-600 dark:text-brand-400">
                            Rp{{ number_format($pkg->price, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="text-right">
                        @if($normalTotal > 0)
                            <span class="text-xs text-slate-500 dark:text-slate-400 block">
                                Normal: <span class="line-through font-mono">Rp{{ number_format($normalTotal, 0, ',', '.') }}</span>
                            </span>
                            @if($saving > 0)
                                <span class="inline-flex items-center gap-1 mt-0.5 text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-500/15 border border-emerald-500/30 px-2 py-0.5 rounded-full">
                                    <i class="fa-solid fa-tag text-[10px]"></i> Hemat Rp{{ number_format($saving, 0, ',', '.') }}
                                </span>
                            @endif
                        @else
                            <span class="text-xs text-slate-400 italic">Belum ada item</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card Body: Included Items -->
            <div class="p-5 sm:p-6 flex-grow space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Isi Paket (Products Included):
                    </h4>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                        {{ $pkg->packageItems->count() }} Menu
                    </span>
                </div>

                @if($pkg->packageItems->count() == 0)
                    <div class="p-6 rounded-xl border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30 text-center">
                        <i class="fa-solid fa-box-open text-slate-300 dark:text-slate-600 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Paket ini masih kosong</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Pilih produk pada formulir di bawah untuk menambahkan item ke dalam paket.</p>
                    </div>
                @else
                    <ul class="space-y-2.5">
                        @foreach($pkg->packageItems as $item)
                        <li class="group flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100/80 dark:bg-slate-900/50 dark:hover:bg-slate-900/80 border border-slate-200/80 dark:border-slate-700/80 transition-all">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-xs bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-500/30 flex-shrink-0">
                                    {{ $item->quantity }}x
                                </div>
                                <div class="min-w-0">
                                    <span class="font-bold text-slate-900 dark:text-slate-100 text-sm block truncate">
                                        {{ $item->product->name ?? 'Produk Dihapus' }}
                                    </span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-mono block">
                                        Harga Normal: Rp{{ number_format($item->product->price ?? 0, 0, ',', '.') }}
                                        @if($item->quantity > 1)
                                            &bull; Total: Rp{{ number_format(($item->product->price ?? 0) * $item->quantity, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <form action="{{ route('admin.package_items.destroy', $item->id) }}" method="POST" class="flex-shrink-0 ml-2" onsubmit="return confirm('Keluarkan {{ $item->product->name ?? 'item' }} dari paket?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition" title="Hapus dari paket">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Card Footer: Add Item Form -->
            <div class="p-4 sm:p-5 bg-slate-50/80 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-700/80 mt-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-2.5">
                    <i class="fa-solid fa-cart-plus text-amber-500 mr-1"></i> Tambah Menu ke Paket
                </span>
                <form action="{{ route('admin.package_items.store') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                    <div class="flex-grow">
                        <select name="product_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                            <option value="">-- Pilih Menu / Produk --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (Rp{{ number_format($p->price, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="quantity" placeholder="Qty" min="1" max="50" value="1" class="w-20 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-3 py-2 text-sm text-center font-bold focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-amber-600 dark:hover:bg-amber-500 text-white font-bold px-4 py-2 rounded-xl text-sm transition shadow-sm whitespace-nowrap">
                            <i class="fa-solid fa-plus text-xs"></i> Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Modal Tambah Paket Baru -->
<div id="addModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Buat Paket Menu Baru</h3>
            </div>
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.packages.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Nama Paket Bundling <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" placeholder="Contoh: Paket Sarapan Hemat Berdua" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Kategori Menu <span class="text-rose-500">*</span>
                </label>
                <select name="category_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Harga Jual Paket (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-400">Rp</span>
                    <input type="number" name="price" placeholder="45000" min="0" step="500" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-3.5 py-2.5 text-sm font-mono text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                </div>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Paket otomatis masuk ke kasir POS dan bisa ditambahkan menu racikannya setelah dibuat.</p>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700/60">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-sm transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white font-bold text-sm shadow-md transition">
                    Simpan Paket
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Paket -->
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Edit Paket Menu</h3>
            </div>
            <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Nama Paket <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="edit_name" name="name" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Kategori Menu <span class="text-rose-500">*</span>
                </label>
                <select id="edit_category_id" name="category_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Harga Jual Paket (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-400">Rp</span>
                    <input type="number" id="edit_price" name="price" min="0" step="500" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-3.5 py-2.5 text-sm font-mono text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700/60">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-sm transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white font-bold text-sm shadow-md transition">
                    Perbarui Paket
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, catId, price) {
    document.getElementById('editForm').action = '/admin/products/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_category_id').value = catId;
    document.getElementById('edit_price').value = price;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection