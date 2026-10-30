@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto mt-12 bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-700">
    <div class="text-center mb-8">
        <div class="inline-block p-4 rounded-full bg-red-500/20 text-red-500 mb-4">
            <i class="fa-solid fa-lock text-4xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-white">Tutup Shift Kasir</h2>
        <p class="text-slate-400 text-sm mt-2">Hitung uang fisik di laci Anda dan masukkan nominalnya di bawah ini.</p>
    </div>
    @if($unfinishedOrders > 0)
    <div class="mb-6 bg-orange-500/20 border border-orange-500 text-orange-200 p-4 rounded-xl flex items-start gap-3">
        <i class="fa-solid fa-triangle-exclamation mt-1"></i>
        <div>
            <h4 class="font-bold">Peringatan!</h4>
            <p class="text-sm">Terdapat <strong>{{ $unfinishedOrders }} pesanan</strong> yang berstatus belum selesai (sedang dimasak/belum disajikan). Apakah Anda yakin ingin menutup shift?</p>
        </div>
    </div>
    @endif
    
    <form id="closeShiftForm" action="{{ route('pos.shift.storeClose') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">Total Uang Fisik (Rp)</label>
            <input type="number" id="closing_cash_actual" name="closing_cash_actual" required min="0" placeholder="0" class="w-full bg-slate-900 border border-slate-600 rounded-xl p-4 text-white text-lg focus:border-red-500 focus:ring focus:ring-red-500/20 outline-none transition">
            <p class="text-xs text-slate-500 mt-2"><i class="fa-solid fa-circle-info mr-1"></i> Masukkan jumlah uang tunai aktual (Blind Close). Selisih akan dihitung otomatis.</p>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">Catatan (Opsional)</label>
            <textarea name="notes" rows="3" placeholder="Alasan selisih uang jika ada..." class="w-full bg-slate-900 border border-slate-600 rounded-xl p-4 text-white text-sm focus:border-red-500 focus:ring focus:ring-red-500/20 outline-none transition"></textarea>
        </div>
        <button type="button" onclick="confirmClose()" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-4 rounded-xl shadow-lg transition duration-300">
            <i class="fa-solid fa-lock mr-2"></i> Konfirmasi & Tutup Shift
        </button>
    </form>
</div>

<script>
    function confirmClose() {
        const actualCash = document.getElementById('closing_cash_actual').value;
        const expectedCash = {{ $expectedCash }};
        const unfinishedOrders = {{ $unfinishedOrders }};
        
        if (!actualCash || actualCash === "") {
            alert("Harap masukkan total uang fisik di laci kasir terlebih dahulu!");
            return;
        }

        let warnings = [];
        
        if (parseFloat(actualCash) !== expectedCash) {
            let diff = Math.abs(parseFloat(actualCash) - expectedCash);
            let diffRp = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(diff);
            let status = parseFloat(actualCash) > expectedCash ? 'lebih' : 'kurang';
            warnings.push("⚠️ Terdapat SELISIH UANG Kasir sebesar " + diffRp + " (" + status + ").");
        }
        
        if (unfinishedOrders > 0) {
            warnings.push("⚠️ Terdapat " + unfinishedOrders + " PESANAN BELUM SELESAI.");
        }
        
        let confirmMessage = "";
        if (warnings.length > 0) {
            confirmMessage = "PERHATIAN!\n\n" + warnings.join("\n\n") + "\n\nApakah Anda tetap yakin ingin menutup shift?";
        } else {
            confirmMessage = "Data sesuai. Apakah Anda yakin ingin menutup shift? Anda tidak dapat membuka transaksi lagi di shift ini.";
        }
        
        if (confirm(confirmMessage)) {
            document.getElementById('closeShiftForm').submit();
        }
    }
</script>
@endsection