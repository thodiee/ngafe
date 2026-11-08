@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-700/60">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-brand-500 flex items-center justify-center text-slate-950 font-bold shadow-md shadow-amber-500/20">
                    <i class="fa-solid fa-layer-group text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Varian Menu & Add-on</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola opsi racikan minuman dan makanan (Level Gula, Suhu, Extra Shot, Topping, dll.)</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-sliders text-amber-500"></i> {{ $variants->count() }} Grup Varian
            </span>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-600 via-amber-500 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white dark:text-slate-950 font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-amber-500/25 transition text-sm">
                <i class="fa-solid fa-plus"></i> Grup Varian Baru
            </button>
        </div>
    </div>

    @if($variants->count() == 0)
    <!-- Empty State -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-12 text-center shadow-sm">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl border border-amber-500/20">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Belum Ada Grup Varian</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">Tambahkan opsi kustomisasi seperti pilihan suhu, ukuran cup, level manis, atau tambahan topping untuk menu Anda.</p>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition text-sm">
            <i class="fa-solid fa-plus"></i> Buat Grup Varian
        </button>
    </div>
    @else
    <!-- Variants Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @foreach($variants as $variant)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden flex flex-col">
            <!-- Header Card -->
            <div class="p-5 bg-slate-50/70 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-700/80">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $variant->is_required ? 'bg-rose-500/10 text-rose-700 dark:text-rose-300 border border-rose-500/20' : 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-500/20' }}">
                                <i class="fa-solid {{ $variant->is_required ? 'fa-asterisk text-[9px]' : 'fa-check text-[9px]' }}"></i>
                                {{ $variant->is_required ? 'Wajib Dipilih' : 'Opsional' }}
                            </span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">{{ $variant->name }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Menu: <span class="font-bold text-amber-600 dark:text-brand-400">{{ $variant->product->name ?? 'Produk Dihapus' }}</span>
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button onclick="openEditVariantModal({{ $variant->id }}, '{{ addslashes($variant->name) }}', {{ $variant->product_id }}, {{ $variant->is_required ? 'true' : 'false' }})" class="w-8 h-8 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-slate-700 transition" title="Edit Grup Varian">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <form action="{{ route('admin.variants.destroy', $variant->id) }}" method="POST" onsubmit="return confirm('Hapus grup varian ini beserta semua opsinya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition" title="Hapus Grup Varian">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Options List -->
            <div class="p-5 flex-grow space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pilihan Opsi Varian:</h4>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                        {{ $variant->options->count() }} Opsi
                    </span>
                </div>

                @if($variant->options->count() == 0)
                    <div class="p-4 rounded-xl border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30 text-center">
                        <p class="text-xs text-slate-400">Belum ada pilihan opsi. Tambahkan di bawah ini.</p>
                    </div>
                @else
                    <ul class="space-y-2">
                        @foreach($variant->options as $opt)
                        <li class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100/80 dark:bg-slate-900/50 dark:hover:bg-slate-900/80 border border-slate-200/80 dark:border-slate-700/80 transition-all">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $opt->name }}</span>
                                @if($opt->price_adjustment > 0)
                                    <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                        +Rp{{ number_format($opt->price_adjustment, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                            <form action="{{ route('admin.variant_options.destroy', $opt->id) }}" method="POST" onsubmit="return confirm('Hapus opsi {{ $opt->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition" title="Hapus Opsi">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Add Option Inline Form -->
            <div class="p-4 bg-slate-50/80 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-700/80 mt-auto">
                <form action="{{ route('admin.variant_options.store') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                    @csrf
                    <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                    <input type="text" name="name" placeholder="Nama Opsi (Cth: Hot / Less Ice)" class="flex-grow bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                    <div class="flex items-center gap-2">
                        <input type="number" name="price_adjustment" placeholder="Tambahan Rp" min="0" step="500" class="w-32 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm font-mono text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" value="0">
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-amber-600 dark:hover:bg-amber-500 text-white font-bold px-3.5 py-2 rounded-xl text-sm transition shadow-sm whitespace-nowrap">
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

<!-- Modal Tambah Grup Varian -->
<div id="addModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900 dark:text-white">Grup Varian Baru</h3>
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.variants.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Pilih Produk Menu <span class="text-rose-500">*</span></label>
                <select name="product_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Nama Grup Varian <span class="text-rose-500">*</span></label>
                <input type="text" name="name" placeholder="Contoh: Suhu / Level Manis / Pilihan Susu" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>
            <div class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                <input type="checkbox" id="is_required" name="is_required" value="1" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-amber-600 focus:ring-amber-500">
                <label for="is_required" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">Varian Wajib Dipilih (Required saat memesan)</label>
            </div>
            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700/60">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-sm transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white font-bold text-sm shadow-md transition">Buat Grup Varian</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Grup Varian -->
<div id="editVariantModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900 dark:text-white">Edit Grup Varian</h3>
            <button type="button" onclick="document.getElementById('editVariantModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="editVariantForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Pilih Produk Menu <span class="text-rose-500">*</span></label>
                <select id="edit_v_product_id" name="product_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Nama Grup Varian <span class="text-rose-500">*</span></label>
                <input type="text" id="edit_v_name" name="name" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>
            <div class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                <input type="checkbox" id="edit_v_is_required" name="is_required" value="1" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-amber-600 focus:ring-amber-500">
                <label for="edit_v_is_required" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">Varian Wajib Dipilih (Required saat memesan)</label>
            </div>
            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700/60">
                <button type="button" onclick="document.getElementById('editVariantModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-sm transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white font-bold text-sm shadow-md transition">Perbarui Grup Varian</button>
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