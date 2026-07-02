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
            <div class="flex gap-2">
                <button onclick="openEditVariantModal({{ $variant->id }}, '{{ addslashes($variant->name) }}', {{ $variant->product_id }}, {{ $variant->is_required ? 'true' : 'false' }})" class="bg-blue-500/10 border border-blue-500/30 text-blue-400 hover:bg-blue-500 hover:text-white px-3 py-2 rounded-lg transition"><i class="fa-solid fa-pen-to-square"></i></button>
                <form action="{{ route('admin.variants.destroy', $variant->id) }}" method="POST" onsubmit="return confirm('Hapus grup varian ini beserta semua opsinya?')">
                    @csrf @method('DELETE')
                    <button class="bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white px-3 py-2 rounded-lg transition"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>
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

<!-- Modal Edit Grup Varian -->
<div id="editVariantModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Edit Grup Varian</h3>
        <form id="editVariantForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Pilih Produk</label>
                <select id="edit_v_product_id" name="product_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Varian</label>
                <input type="text" id="edit_v_name" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-6 flex items-center">
                <input type="checkbox" id="edit_v_is_required" name="is_required" value="1" class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-brand-600 focus:ring-brand-600">
                <label for="edit_v_is_required" class="ml-2 text-sm font-medium text-slate-300">Varian Wajib Dipilih (Required)</label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editVariantModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditVariantModal(id, name, productId, isRequired) {
    document.getElementById('editVariantForm').action = '/admin/variants/' + id;
    document.getElementById('edit_v_product_id').value = productId;
    document.getElementById('edit_v_name').value = name;
    document.getElementById('edit_v_is_required').checked = isRequired;
    document.getElementById('editVariantModal').classList.remove('hidden');
}
</script>
@endsection