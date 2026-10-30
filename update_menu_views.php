<?php
$viewsDir = __DIR__ . '/resources/views/admin';

// 1. Update products.blade.php
$productsFile = $viewsDir . '/products.blade.php';
$productsContent = file_get_contents($productsFile);

$productsContent = preg_replace(
    '/<td class="p-4 font-bold">{{ \$prod->name }}<\/td>/',
    '<td class="p-4">
                    <span class="font-bold">{{ $prod->name }}</span>
                    @if($prod->is_package)
                        <span class="ml-2 bg-amber-500/20 text-amber-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-amber-500/30"><i class="fa-solid fa-box-open mr-1"></i> Paket</span>
                    @endif
                </td>',
    $productsContent
);

$productsContent = preg_replace(
    '/<form action="{{ route\(\'admin\.products\.destroy\'/',
    '<button onclick="openEditModal({{ $prod->id }}, \'{{ addslashes($prod->name) }}\', {{ $prod->category_id }}, {{ $prod->price }})" class="text-blue-400 hover:text-blue-300 ml-2"><i class="fa-solid fa-pen-to-square"></i></button>
                    <form action="{{ route(\'admin.products.destroy\'',
    $productsContent
);

$editModalProducts = <<<'BLADE'

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Edit Menu</h3>
        <form id="editForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Menu</label>
                <input type="text" id="edit_name" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Kategori</label>
                <select id="edit_category_id" name="category_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Harga (Rp)</label>
                <input type="number" id="edit_price" name="price" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Update</button>
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
BLADE;

$productsContent = str_replace('@endsection', $editModalProducts, $productsContent);
file_put_contents($productsFile, $productsContent);

// 2. Create variants.blade.php
$variantsView = <<<'BLADE'
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Varian Menu</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition"><i class="fa-solid fa-plus mr-1"></i> Grup Varian Baru</button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($variants as $variant)
    <div class="bg-slate-800 rounded-2xl border border-slate-700 shadow-lg overflow-hidden flex flex-col">
        <div class="bg-slate-900 p-4 border-b border-slate-700 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-white">{{ $variant->name }}</h3>
                <p class="text-sm text-slate-400">Terpasang pada: <span class="text-brand-400 font-semibold">{{ $variant->product->name }}</span></p>
                @if($variant->is_required)
                    <span class="inline-block mt-1 bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-red-500/30">Wajib Dipilih</span>
                @else
                    <span class="inline-block mt-1 bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-blue-500/30">Opsional</span>
                @endif
            </div>
            <form action="{{ route('admin.variants.destroy', $variant->id) }}" method="POST" onsubmit="return confirm('Hapus grup varian ini beserta semua opsinya?')">
                @csrf @method('DELETE')
                <button class="bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white px-3 py-2 rounded-lg transition"><i class="fa-solid fa-trash"></i></button>
            </form>
        </div>
        
        <div class="p-4 flex-grow">
            <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Opsi Varian:</h4>
            @if($variant->options->count() == 0)
                <p class="text-sm text-slate-500 italic">Belum ada opsi. Tambahkan di bawah.</p>
            @else
                <ul class="space-y-2 mb-4">
                    @foreach($variant->options as $opt)
                    <li class="flex justify-between items-center bg-slate-900/50 p-2 rounded border border-slate-700/50">
                        <div>
                            <span class="font-semibold text-slate-200">{{ $opt->name }}</span>
                            @if($opt->price_adjustment > 0)
                                <span class="ml-2 text-xs text-brand-400 font-bold">+Rp{{ number_format($opt->price_adjustment, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        <form action="{{ route('admin.variant_options.destroy', $opt->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-slate-500 hover:text-red-400"><i class="fa-solid fa-xmark"></i></button>
                        </form>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
        
        <div class="bg-slate-900/80 p-3 border-t border-slate-700">
            <form action="{{ route('admin.variant_options.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                <input type="text" name="name" placeholder="Nama Opsi (Cth: Hot)" class="flex-1 bg-slate-800 border border-slate-600 rounded px-3 py-1.5 text-sm text-white focus:outline-none focus:border-brand-500" required>
                <input type="number" name="price_adjustment" placeholder="Harga (+)" class="w-24 bg-slate-800 border border-slate-600 rounded px-3 py-1.5 text-sm text-white focus:outline-none focus:border-brand-500" value="0">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded text-sm font-bold transition"><i class="fa-solid fa-plus"></i></button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<!-- Modal Tambah Grup Varian -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Grup Varian Baru</h3>
        <form action="{{ route('admin.variants.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Pilih Produk</label>
                <select name="product_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Varian (Cth: Suhu, Level Gula, Ukuran)</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-6 flex items-center">
                <input type="checkbox" id="is_required" name="is_required" value="1" class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-brand-600 focus:ring-brand-600">
                <label for="is_required" class="ml-2 text-sm font-medium text-slate-300">Varian Wajib Dipilih (Required)</label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Buat Grup Varian</button>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE;
file_put_contents($viewsDir . '/variants.blade.php', $variantsView);

// 3. Create packages.blade.php
$packagesView = <<<'BLADE'
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Paket Menu</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-amber-600 hover:bg-amber-500 text-white px-4 py-2 rounded-lg font-bold transition"><i class="fa-solid fa-plus mr-1"></i> Buat Paket Baru</button>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    @foreach($packages as $pkg)
    <div class="bg-slate-800 rounded-2xl border border-slate-700 shadow-lg overflow-hidden flex flex-col relative">
        <div class="absolute top-0 right-0 bg-amber-500 text-amber-950 px-3 py-1 rounded-bl-xl font-bold text-xs"><i class="fa-solid fa-box-open mr-1"></i> PAKET BUNDLING</div>
        <div class="bg-slate-900 p-5 border-b border-slate-700 flex justify-between items-start">
            <div>
                <h3 class="text-xl font-bold text-white mb-1">{{ $pkg->name }}</h3>
                <p class="text-brand-400 font-bold text-lg mb-2">Harga Jual: Rp{{ number_format($pkg->price, 0, ',', '.') }}</p>
                <span class="bg-purple-500/20 text-purple-400 px-2 py-0.5 rounded text-[10px] font-bold">{{ $pkg->category->name ?? 'Kategori' }}</span>
            </div>
            <div class="flex flex-col gap-2 mt-4">
                <form action="{{ route('admin.products.destroy', $pkg->id) }}" method="POST" onsubmit="return confirm('Hapus paket ini secara permanen?')">
                    @csrf @method('DELETE')
                    <button class="bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white px-3 py-1.5 rounded-lg transition text-sm"><i class="fa-solid fa-trash mr-1"></i> Hapus Paket</button>
                </form>
            </div>
        </div>
        
        <div class="p-5 flex-grow">
            <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Isi Paket (Products Included):</h4>
            @if($pkg->packageItems->count() == 0)
                <p class="text-sm text-slate-500 italic bg-slate-900/50 p-4 rounded-lg text-center border border-dashed border-slate-600">Paket ini masih kosong. Silakan tambahkan produk ke dalam paket.</p>
            @else
                <ul class="space-y-3 mb-4">
                    @foreach($pkg->packageItems as $item)
                    <li class="flex justify-between items-center bg-slate-900/80 p-3 rounded-xl border border-slate-700 shadow-sm">
                        <div class="flex items-center">
                            <div class="bg-slate-800 w-8 h-8 rounded-full flex items-center justify-center font-bold text-brand-400 mr-3 border border-slate-600">{{ $item->quantity }}x</div>
                            <div>
                                <span class="font-semibold text-slate-200 block">{{ $item->product->name }}</span>
                                <span class="text-xs text-slate-500">Harga Normal: Rp{{ number_format($item->product->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <form action="{{ route('admin.package_items.destroy', $item->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-slate-500 hover:text-red-400 bg-slate-800 w-8 h-8 rounded-full flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                
                <div class="mt-4 pt-4 border-t border-slate-700/50 flex justify-between items-center text-sm">
                    <span class="text-slate-400">Total Harga Normal Isi Paket:</span>
                    @php
                        $normalTotal = $pkg->packageItems->sum(function($item) { return $item->product->price * $item->quantity; });
                        $saving = $normalTotal - $pkg->price;
                    @endphp
                    <span class="font-bold text-slate-300">Rp{{ number_format($normalTotal, 0, ',', '.') }}</span>
                </div>
                @if($saving > 0)
                <div class="flex justify-between items-center text-sm mt-1">
                    <span class="text-emerald-400 font-medium">Hemat:</span>
                    <span class="font-bold text-emerald-400">Rp{{ number_format($saving, 0, ',', '.') }}</span>
                </div>
                @endif
            @endif
        </div>
        
        <div class="bg-slate-900/80 p-4 border-t border-slate-700">
            <h5 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tambah Isi Paket</h5>
            <form action="{{ route('admin.package_items.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                <select name="product_id" class="flex-grow bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-brand-500" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Rp{{ number_format($p->price, 0, ',', '.') }})</option>
                    @endforeach
                </select>
                <input type="number" name="quantity" placeholder="Qty" min="1" value="1" class="w-20 bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-sm text-center text-white focus:outline-none focus:border-brand-500" required>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-bold transition">Tambah</button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<!-- Modal Tambah Paket -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Buat Paket Menu Baru</h3>
        <p class="text-sm text-slate-400 mb-4">Paket akan dibuat sebagai produk yang bisa dibeli langsung di Kasir. Anda dapat mengisi item-item di dalam paket setelah paket dibuat.</p>
        <form action="{{ route('admin.packages.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Paket (Cth: Paket Hemat Berdua)</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Kategori Paket</label>
                <select name="category_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Harga Paket (Total Bundling Rp)</label>
                <input type="number" name="price" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white font-bold rounded hover:bg-amber-500">Buat Paket</button>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE;
file_put_contents($viewsDir . '/packages.blade.php', $packagesView);

echo "Admin Menu Views Created/Updated.\n";
