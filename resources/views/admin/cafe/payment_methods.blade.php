@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-violet-500/20 text-violet-400 border border-violet-500/30">
                <i class="fa-solid fa-wallet text-base"></i>
            </span>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Metode Pembayaran Kasir</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola opsi kanal pembayaran yang tersedia pada sistem kasir (POS).</p>
            </div>
        </div>
        <div>
            <button onclick="openPayModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-violet-600/20 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Metode</span>
            </button>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($methods as $method)
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl {{ $method->is_active ? 'bg-violet-500/15 text-violet-600 dark:text-violet-400 border border-violet-500/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-400' }} flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid {{ $method->icon ?? 'fa-credit-card' }}"></i>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white text-base">{{ $method->name }}</div>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $method->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                <span class="w-2 h-2 rounded-full {{ $method->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $method->is_active ? 'Aktif di Kasir' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button onclick="editPayMethod({{ $method->id }}, '{{ addslashes($method->name) }}', '{{ addslashes($method->icon) }}', {{ $method->is_active ? 1 : 0 }}, {{ $method->has_surcharge ? 1 : 0 }}, {{ $method->surcharge_rate }})" class="p-2 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 hover:bg-brand-500/20 transition" title="Edit">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                        <form action="{{ route('admin.cafe.payment_methods.destroy', $method) }}" method="POST" class="inline" onsubmit="return confirm('Hapus metode {{ $method->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 transition" title="Hapus">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

                @if($method->has_surcharge)
                <div class="inline-flex items-center gap-1.5 text-xs bg-orange-500/10 text-orange-600 dark:text-orange-400 px-3 py-1.5 rounded-xl border border-orange-500/20 font-medium">
                    <i class="fa-solid fa-percent text-[10px]"></i>
                    <span>Surcharge / Biaya Tambahan: <b>{{ $method->surcharge_rate }}%</b></span>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-wallet text-5xl mb-3 opacity-30"></i>
            <p>Belum ada metode pembayaran yang terdaftar.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah/Edit Metode -->
<div id="payModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-7 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
            <h3 id="payModalTitle" class="text-lg font-bold text-slate-900 dark:text-white">Tambah Metode Pembayaran</h3>
            <button onclick="closePayModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="payForm" action="{{ route('admin.cafe.payment_methods.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="payMethod" value="POST">
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Metode</label>
                <input type="text" name="name" id="payName" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-violet-500 outline-none transition" placeholder="Contoh: QRIS, Tunai, Kartu Debit BCA" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Ikon (Font Awesome class)</label>
                <div class="flex gap-2">
                    <input type="text" name="icon" id="payIcon" class="flex-1 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-violet-500 outline-none transition" placeholder="fa-qrcode">
                    <div class="w-11 h-11 flex items-center justify-center bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-violet-500 text-lg shrink-0" id="iconPreview">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Pilihan: fa-money-bill, fa-qrcode, fa-credit-card, fa-mobile-screen, fa-building-columns</p>
            </div>

            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Status Aktif di Kasir</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="payActive" value="1" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-violet-600 rounded-full peer transition-colors"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </label>
            </div>

            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Biaya Tambahan (Surcharge)?</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="has_surcharge" id="paySurcharge" value="1" class="sr-only peer" onchange="toggleSurcharge(this)">
                    <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-orange-500 rounded-full peer transition-colors"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </label>
            </div>

            <div id="surchargeRateInput" class="hidden">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Persentase Biaya Tambahan (%)</label>
                <input type="number" name="surcharge_rate" id="paySurchargeRate" step="0.01" min="0" max="100" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm focus:border-orange-500 outline-none transition" placeholder="Contoh: 1.5">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closePayModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition text-sm">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 text-white font-bold shadow-lg shadow-violet-600/20 active:scale-95 transition text-sm">
                    Simpan Metode
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('payIcon').addEventListener('input', function() {
    document.getElementById('iconPreview').innerHTML = `<i class="fa-solid ${this.value}"></i>`;
});

function toggleSurcharge(cb) {
    document.getElementById('surchargeRateInput').classList.toggle('hidden', !cb.checked);
}

function openPayModal() {
    document.getElementById('payModalTitle').innerText = 'Tambah Metode Pembayaran';
    document.getElementById('payForm').action = "{{ route('admin.cafe.payment_methods.store') }}";
    document.getElementById('payMethod').value = 'POST';
    document.getElementById('payForm').reset();
    document.getElementById('surchargeRateInput').classList.add('hidden');
    document.getElementById('iconPreview').innerHTML = '<i class="fa-solid fa-credit-card"></i>';
    document.getElementById('payModal').classList.remove('hidden');
    document.getElementById('payModal').classList.add('flex');
}

function editPayMethod(id, name, icon, isActive, hasSurcharge, surchargeRate) {
    document.getElementById('payModalTitle').innerText = 'Edit Metode: ' + name;
    document.getElementById('payForm').action = '/admin/cafe/payment-methods/' + id;
    document.getElementById('payMethod').value = 'PUT';
    document.getElementById('payName').value = name;
    document.getElementById('payIcon').value = icon;
    document.getElementById('iconPreview').innerHTML = `<i class="fa-solid ${icon}"></i>`;
    document.getElementById('payActive').checked = isActive === 1;
    document.getElementById('paySurcharge').checked = hasSurcharge === 1;
    document.getElementById('paySurchargeRate').value = surchargeRate;
    document.getElementById('surchargeRateInput').classList.toggle('hidden', hasSurcharge !== 1);
    document.getElementById('payModal').classList.remove('hidden');
    document.getElementById('payModal').classList.add('flex');
}

function closePayModal() {
    document.getElementById('payModal').classList.add('hidden');
    document.getElementById('payModal').classList.remove('flex');
}
</script>
@endsection
