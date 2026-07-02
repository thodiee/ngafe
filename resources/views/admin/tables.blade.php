@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-slate-100">Pengaturan Meja</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Meja</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nomor Meja</th>
                <th class="p-4">Area</th>
                <th class="p-4">QR Self-Service</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-slate-200">
            @foreach($tables as $table)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold text-slate-100">{{ $table->table_number }}</td>
                <td class="p-4">
                    <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs font-bold uppercase">{{ $table->area->name ?? 'Tanpa Area' }}</span>
                </td>
                <td class="p-4">
                    @if($table->qr_token)
                        <button onclick="showQrModal('{{ $table->table_number }}', '{{ route('self-service.table', $table->qr_token) }}')" class="bg-brand-500/20 text-brand-400 border border-brand-500/30 hover:bg-brand-500 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition">
                            <i class="fa-solid fa-qrcode"></i> Lihat & Cetak QR
                        </button>
                    @else
                        <span class="text-slate-500 text-xs italic">Token Kosong</span>
                    @endif
                </td>
                <td class="p-4 text-right">
                    <button onclick="openEditModal({{ $table->id }}, '{{ addslashes($table->table_number) }}', '{{ $table->area_id }}')" class="text-blue-400 hover:text-blue-300 ml-2"><i class="fa-solid fa-pen-to-square"></i></button>
                    <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus meja ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 ml-2"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4 text-slate-100">Tambah Meja Baru</h3>
        <form action="{{ route('admin.tables.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nomor / Nama Meja</label>
                <input type="text" name="table_number" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" placeholder="M-01" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Area</label>
                <select name="area_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    @foreach($areas as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4 text-slate-100">Edit Meja</h3>
        <form id="editForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nomor / Nama Meja</label>
                <input type="text" id="edit_table_number" name="table_number" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Area</label>
                <select id="edit_area_id" name="area_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    @foreach($areas as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal QR Code (Printable) -->
<div id="qrModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-white text-slate-800 p-8 rounded-3xl w-full max-w-sm border border-slate-200 shadow-2xl text-center flex flex-col items-center gap-6 relative">
        <button onclick="document.getElementById('qrModal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-xl">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <div id="printArea" class="flex flex-col items-center gap-4 w-full">
            <div class="flex items-center gap-2 text-brand-600 font-extrabold text-2xl tracking-wide">
                <i class="fa-solid fa-mug-hot text-amber-500"></i> NGAFE
            </div>
            <div class="w-full h-px bg-slate-200 my-2"></div>
            <h4 class="font-extrabold text-3xl text-slate-900 tracking-tight" id="qrModalTableTitle">MEJA -</h4>
            <p class="text-xs text-slate-500 max-w-[200px] leading-relaxed">Pindai kode QR di bawah ini untuk memesan langsung dari ponsel Anda</p>
            
            <div class="bg-slate-50 p-4 rounded-3xl border border-slate-100 flex items-center justify-center shadow-inner">
                <img id="qrModalImage" src="" alt="QR Code" class="w-48 h-48">
            </div>
            
            <span class="text-[10px] text-slate-400 font-mono tracking-wider break-all hidden" id="qrModalLink"></span>
        </div>
        
        <div class="flex gap-2 w-full mt-2">
            <button onclick="document.getElementById('qrModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-600 text-sm font-medium transition">Tutup</button>
            <button onclick="printQrCard()" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-bold shadow-md shadow-emerald-500/10 transition flex items-center justify-center gap-2">
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
                <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&display=swap" rel="stylesheet">
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
                        font-weight: 800;
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