@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Reservasi Meja</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Buat Reservasi Baru</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Waktu Reservasi</th>
                <th class="p-4">Atas Nama</th>
                <th class="p-4">Telepon</th>
                <th class="p-4">Meja / Area</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $res)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4">
                    <div class="font-bold text-white">{{ $res->reservation_time->format('d M Y') }}</div>
                    <div class="text-sm text-slate-400">{{ $res->reservation_time->format('H:i') }}</div>
                </td>
                <td class="p-4 font-bold">{{ $res->customer_name }}</td>
                <td class="p-4">{{ $res->customer_phone }}</td>
                <td class="p-4">
                    <span class="font-bold text-slate-200">Meja {{ $res->table->table_number ?? 'N/A' }}</span>
                    <span class="text-xs text-brand-400 font-semibold block uppercase tracking-wider mt-1">{{ $res->table->area->name ?? 'Tanpa Area' }}</span>
                </td>
                <td class="p-4">
                    @if($res->status === 'pending')
                        <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs font-bold uppercase">Pending</span>
                    @elseif($res->status === 'confirmed')
                        <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs font-bold uppercase">Confirmed</span>
                    @elseif($res->status === 'cancelled')
                        <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs font-bold uppercase">Cancelled</span>
                    @elseif($res->status === 'completed')
                        <span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded text-xs font-bold uppercase">Completed</span>
                    @endif
                </td>
                <td class="p-4 text-right">
                    <form action="{{ route('admin.reservations.update', $res->id) }}" method="POST" class="inline-flex items-center gap-2">
                        @csrf @method('POST')
                        <div class="relative">
                            <select name="status" onchange="this.form.submit()" 
                                class="appearance-none bg-slate-900/50 border-2 
                                {{ $res->status == 'pending' ? 'border-yellow-500/50 text-yellow-400 focus:border-yellow-500' : '' }}
                                {{ $res->status == 'confirmed' ? 'border-blue-500/50 text-blue-400 focus:border-blue-500' : '' }}
                                {{ $res->status == 'completed' ? 'border-emerald-500/50 text-emerald-400 focus:border-emerald-500' : '' }}
                                {{ $res->status == 'cancelled' ? 'border-red-500/50 text-red-400 focus:border-red-500' : '' }}
                                rounded-lg text-xs font-bold uppercase px-3 py-1.5 pr-8 focus:outline-none transition cursor-pointer hover:bg-slate-800">
                                <option value="pending" {{ $res->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $res->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ $res->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $res->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </form>
                    <form action="{{ route('admin.reservations.destroy', $res->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data reservasi ini?')">
                        @csrf @method('DELETE')
                        <button class="bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white px-2.5 py-1.5 rounded-lg transition ml-1"><i class="fa-solid fa-trash"></i></button>
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
        <h3 class="text-xl font-bold mb-4">Buat Reservasi</h3>
        <form action="{{ route('admin.reservations.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Pilih Meja</label>
                <select name="table_id" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
                    @foreach($tables as $table)
                        <option value="{{ $table->id }}">Meja {{ $table->table_number }} ({{ strtoupper($table->area->name ?? 'TANPA AREA') }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Atas Nama (Customer)</label>
                <input type="text" name="customer_name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1 text-slate-400">Nomor Telepon</label>
                <input type="text" name="customer_phone" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Tanggal & Waktu</label>
                <input type="datetime-local" name="reservation_time" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan Reservasi</button>
            </div>
        </form>
    </div>
</div>
@endsection