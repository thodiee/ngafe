@extends('admin.layout')
@section('content')
@if(session('success'))
<div class="mb-4 bg-emerald-500/20 text-emerald-400 p-4 rounded-xl border border-emerald-500/30"><i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif

<div class="flex justify-between items-center mb-6">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-violet-500/20 text-violet-400 rounded-xl flex items-center justify-center text-2xl"><i class="fa-solid fa-wallet"></i></div>
        <div>
            <h2 class="text-3xl font-bold">Metode Pembayaran</h2>
            <p class="text-slate-400">Atur metode bayar yang tersedia di kasir.</p>
        </div>
    </div>
    <button onclick="openPayModal()" class="bg-violet-600 hover:bg-violet-500 text-white px-4 py-2 rounded-lg font-bold transition">
        <i class="fa-solid fa-plus mr-2"></i>Tambah Metode
    </button>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($methods as $method)
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $method->is_active ? 'bg-violet-500/20 text-violet-400' : 'bg-slate-700 text-slate-500' }} flex items-center justify-center text-lg">
                    <i class="fa-solid {{ $method->icon ?? 'fa-credit-card' }}"></i>
                </div>
                <div>
                    <div class="font-bold">{{ $method->name }}</div>
                    <div class="text-xs {{ $method->is_active ? 'text-emerald-400' : 'text-slate-500' }}">
                        {{ $method->is_active ? 'Aktif' : 'Nonaktif' }}
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="editPayMethod({{ $method->id }}, '{{ $method->name }}', '{{ $method->icon }}', {{ $method->is_active ? 1 : 0 }}, {{ $method->has_surcharge ? 1 : 0 }}, {{ $method->surcharge_rate }})" class="text-blue-400 hover:text-blue-300 text-sm"><i class="fa-solid fa-edit"></i></button>
                <form action="{{ route('admin.cafe.payment_methods.destroy', $method) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button class="text-red-400 hover:text-red-300 text-sm"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>
        </div>
        @if($method->has_surcharge)
        <div class="text-xs bg-orange-500/10 text-orange-400 px-3 py-1 rounded-lg border border-orange-500/20">
            <i class="fa-solid fa-percent mr-1"></i>Biaya tambahan: {{ $method->surcharge_rate }}%
        </div>
        @endif
    </div>
    @empty
    <div class="col-span-3 text-center py-16 text-slate-400">
        <i class="fa-solid fa-wallet text-5xl mb-4 block opacity-30"></i>
        Belum ada metode pembayaran. Tambahkan yang pertama.
    </div>
    @endforelse
</div>

<!-- Modal Tambah/Edit -->
<div id="payModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 id="payModalTitle" class="text-xl font-bold mb-5">Tambah Metode Pembayaran</h3>
        <form id="payForm" action="{{ route('admin.cafe.payment_methods.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="payMethod" value="POST">
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nama Metode</label>
                <input type="text" name="name" id="payName" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-violet-500 outline-none transition" placeholder="cth: QRIS, Cash, Transfer BCA" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Ikon (Font Awesome class)</label>
                <div class="flex gap-2">
                    <input type="text" name="icon" id="payIcon" class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-violet-500 outline-none transition" placeholder="fa-qrcode">
                    <div class="w-12 h-12 flex items-center justify-center bg-slate-900 border border-slate-700 rounded-xl text-violet-400 text-xl" id="iconPreview"><i class="fa-solid fa-credit-card"></i></div>
                </div>
                <p class="text-xs text-slate-500 mt-1">Pilih dari: fa-money-bill, fa-qrcode, fa-credit-card, fa-mobile-screen, fa-university</p>
            </div>
            <div class="mb-4 flex items-center justify-between bg-slate-900 px-4 py-3 rounded-xl border border-slate-700">
                <span class="text-sm text-slate-400">Status Aktif</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="payActive" value="1" class="sr-only peer" checked>
                    <div class="w-10 h-5 bg-slate-600 peer-checked:bg-violet-600 rounded-full peer transition-colors"></div>
                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </label>
            </div>
            <div class="mb-4 flex items-center justify-between bg-slate-900 px-4 py-3 rounded-xl border border-slate-700">
                <span class="text-sm text-slate-400">Ada Biaya Tambahan (Surcharge)?</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="has_surcharge" id="paySurcharge" value="1" class="sr-only peer" onchange="toggleSurcharge(this)">
                    <div class="w-10 h-5 bg-slate-600 peer-checked:bg-orange-500 rounded-full peer transition-colors"></div>
                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </label>
            </div>
            <div id="surchargeRateInput" class="mb-5 hidden">
                <label class="block text-sm mb-1 text-slate-400">Persentase Biaya Tambahan (%)</label>
                <input type="number" name="surcharge_rate" id="paySurchargeRate" step="0.01" min="0" max="100" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-orange-500 outline-none transition" placeholder="cth: 2.5">
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closePayModal()" class="flex-1 py-3 bg-slate-700 rounded-xl text-white">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-violet-600 hover:bg-violet-500 text-white font-bold rounded-xl">Simpan</button>
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
}

function editPayMethod(id, name, icon, isActive, hasSurcharge, surchargeRate) {
    document.getElementById('payModalTitle').innerText = 'Edit Metode Pembayaran';
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
}

function closePayModal() {
    document.getElementById('payModal').classList.add('hidden');
}
</script>
@endsection
