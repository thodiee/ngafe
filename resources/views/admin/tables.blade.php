@extends('admin.layout')
@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-700/60">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-brand-500 flex items-center justify-center text-slate-950 font-bold shadow-md shadow-amber-500/20">
                    <i class="fa-solid fa-chair text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Pengaturan Meja</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola daftar nomor meja, area penempatan, dan kode QR untuk pemesanan mandiri (Self-Service)</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-border-all text-amber-500"></i> {{ $tables->count() }} Meja Terdaftar
            </span>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-600 via-amber-500 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white dark:text-slate-950 font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 transition-all duration-200 text-sm">
                <i class="fa-solid fa-plus"></i> Tambah Meja
            </button>
        </div>
    </div>

    @if($tables->count() == 0)
    <!-- Empty State -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-12 text-center shadow-sm">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl border border-amber-500/20">
            <i class="fa-solid fa-chair"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Belum Ada Meja Terdaftar</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">Tambahkan nomor meja pertama Anda untuk mengaktifkan pemesanan POS kasir dan cetak barcode QR self-service.</p>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition text-sm">
            <i class="fa-solid fa-plus"></i> Tambah Meja Sekarang
        </button>
    </div>
    @else
    <!-- Tables Card Wrapper -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700/80 text-xs uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400">
                        <th class="py-4 px-6">Nomor Meja</th>
                        <th class="py-4 px-6">Area Penempatan</th>
                        <th class="py-4 px-6">QR Code Self-Service</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-800 dark:text-slate-200">
                    @foreach($tables as $table)
                    <tr class="hover:bg-amber-500/[0.04] dark:hover:bg-slate-700/30 transition-colors">
                        <!-- Nomor Meja -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-500/10 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 font-black text-sm flex items-center justify-center border border-amber-500/25 flex-shrink-0">
                                    {{ $table->table_number }}
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-900 dark:text-slate-100 text-sm block">
                                        Meja {{ $table->table_number }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">
                                        ID #{{ $table->id }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Area -->
                        <td class="py-4 px-6">
                            @if($table->area)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-500/20">
                                    <i class="fa-solid fa-map-pin text-[10px]"></i> {{ $table->area->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">
                                    Tanpa Area
                                </span>
                            @endif
                        </td>

                        <!-- QR Code Self-Service -->
                        <td class="py-4 px-6">
                            @if($table->qr_token)
                                <div class="flex items-center gap-2">
                                    <button onclick="showQrModal('{{ $table->table_number }}', '{{ route('self-service.table', $table->qr_token) }}')" class="inline-flex items-center gap-2 bg-amber-500/10 hover:bg-amber-500 text-amber-700 dark:text-amber-300 hover:text-white dark:hover:text-slate-950 border border-amber-500/30 hover:border-transparent px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                                        <i class="fa-solid fa-qrcode"></i> Lihat & Cetak QR
                                    </button>
                                    <a href="{{ route('self-service.table', $table->qr_token) }}" target="_blank" class="w-8 h-8 rounded-lg inline-flex items-center justify-center text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition" title="Buka Menu Pelanggan (Tab Baru)">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                    </a>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs text-rose-500 bg-rose-500/10 px-2.5 py-1 rounded-full border border-rose-500/20 font-medium">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Token Belum Tersedia
                                </span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-4 px-6 text-right">
                            <div class="inline-flex items-center gap-1">
                                <button onclick="openEditModal({{ $table->id }}, '{{ addslashes($table->table_number) }}', '{{ $table->area_id }}')" class="w-8 h-8 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-slate-700 transition" title="Edit Meja">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </button>
                                <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus Meja {{ $table->table_number }} secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition" title="Hapus Meja">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Modal Tambah Meja Baru -->
<div id="addModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-chair"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Tambah Meja Baru</h3>
            </div>
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.tables.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Nomor / Nama Meja <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="table_number" placeholder="Contoh: T01 atau VIP-01" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Area Penempatan <span class="text-rose-500">*</span>
                </label>
                <select name="area_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                    <option value="">-- Pilih Area --</option>
                    @foreach($areas as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700/60">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-sm transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white font-bold text-sm shadow-md transition">
                    Simpan Meja
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Meja -->
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Edit Meja</h3>
            </div>
            <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Nomor / Nama Meja <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="edit_table_number" name="table_number" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Area Penempatan <span class="text-rose-500">*</span>
                </label>
                <select id="edit_area_id" name="area_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" required>
                    @foreach($areas as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700/60">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-sm transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white font-bold text-sm shadow-md transition">
                    Perbarui Meja
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal QR Code (Printable) -->
<div id="qrModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 flex p-4 transition-opacity">
    <div class="bg-white text-slate-800 p-8 rounded-3xl w-full max-w-sm border border-slate-200 shadow-2xl text-center flex flex-col items-center gap-6 relative animate-fade-in-up">
        <button onclick="document.getElementById('qrModal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-xl transition">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <div id="printArea" class="flex flex-col items-center gap-4 w-full">
            <div class="flex items-center gap-2 text-brand-600 font-extrabold text-2xl tracking-wide">
                <i class="fa-solid fa-mug-hot text-amber-500"></i> NGAFE
            </div>
            <div class="w-full h-px bg-slate-200 my-1"></div>
            <h4 class="font-black text-3xl text-slate-900 tracking-tight" id="qrModalTableTitle">MEJA -</h4>
            <p class="text-xs text-slate-500 max-w-[220px] leading-relaxed">Pindai kode QR di bawah ini untuk memesan langsung dari ponsel Anda</p>
            
            <div class="bg-slate-50 p-4 rounded-3xl border border-slate-100 flex items-center justify-center shadow-inner">
                <img id="qrModalImage" src="" alt="QR Code" class="w-48 h-48">
            </div>
            
            <span class="text-[10px] text-slate-400 font-mono tracking-wider break-all hidden" id="qrModalLink"></span>
        </div>
        
        <div class="flex gap-2.5 w-full mt-2">
            <button onclick="document.getElementById('qrModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-600 text-sm font-semibold transition">
                Tutup
            </button>
            <button onclick="printQrCard()" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-amber-600 to-brand-500 hover:from-amber-500 hover:to-brand-400 text-white rounded-xl text-sm font-bold shadow-md shadow-amber-500/20 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak QR
            </button>
        </div>
    </div>
</div>

<script>
function openEditModal(id, number, area_id) {
    document.getElementById('editForm').action = '/admin/tables/' + id;
    document.getElementById('edit_table_number').value = number;
    document.getElementById('edit_area_id').value = area_id;
    document.getElementById('editModal').classList.remove('hidden');
}

function showQrModal(tableNumber, linkUrl) {
    document.getElementById('qrModalTableTitle').innerText = 'MEJA ' + tableNumber;
    document.getElementById('qrModalImage').src = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(linkUrl);
    document.getElementById('qrModalLink').innerText = linkUrl;
    document.getElementById('qrModal').classList.remove('hidden');
}

function printQrCard() {
    const tableTitle = document.getElementById('qrModalTableTitle').innerText;
    const qrImageSrc = document.getElementById('qrModalImage').src;
    const linkUrl = document.getElementById('qrModalLink').innerText;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Cetak QR - ${tableTitle}</title>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800;900&display=swap" rel="stylesheet">
                <style>
                    body {
                        font-family: 'Outfit', sans-serif;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        margin: 0;
                        background: white;
                    }
                    .card {
                        text-align: center;
                        border: 3px solid #edf2f7;
                        border-radius: 32px;
                        padding: 30px;
                        width: 280px;
                        background: #ffffff;
                    }
                    .brand {
                        font-size: 24px;
                        font-weight: 800;
                        color: #b08e26;
                        letter-spacing: 2px;
                        margin-bottom: 5px;
                    }
                    .separator {
                        height: 2px;
                        background: #edf2f7;
                        width: 100%;
                        margin: 10px 0;
                    }
                    h4 { 
                        margin: 10px 0; 
                        font-size: 32px; 
                        color: #1a202c; 
                        font-weight: 900;
                    }
                    p { 
                        font-size: 12px; 
                        color: #718096; 
                        margin-bottom: 20px; 
                        line-height: 1.5; 
                    }
                    img { 
                        margin: 10px 0; 
                        border: 1px solid #e2e8f0; 
                        border-radius: 20px; 
                        padding: 10px; 
                        background: #f7fafc; 
                    }
                    .link { 
                        font-size: 8px; 
                        color: #a0aec0; 
                        word-break: break-all; 
                        margin-top: 15px; 
                        font-family: monospace; 
                    }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                <div class="card">
                    <div class="brand">
                        <i class="fa-solid fa-mug-hot"></i> NGAFE
                    </div>
                    <div class="separator"></div>
                    <h4>${tableTitle}</h4>
                    <p>Pindai kode QR di bawah ini untuk memesan langsung dari ponsel Anda</p>
                    <img src="${qrImageSrc}" width="200" height="200">
                    <div class="link">${linkUrl}</div>
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endsection