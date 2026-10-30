@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Data Pelanggan (Member)</h2>
    <a href="{{ route('admin.customers.create') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg font-semibold transition"><i class="fa-solid fa-plus mr-2"></i> Tambah Pelanggan</a>
</div>
<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nama</th>
                <th class="p-4">No HP</th>
                <th class="p-4">Alamat</th>
                <th class="p-4">Tanggal Lahir</th>
                <th class="p-4">Total Poin</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold text-white">{{ $item->name }}</td>
                <td class="p-4">{{ $item->phone }}</td>
                <td class="p-4 text-sm">{{ $item->address ?? '-' }}</td>
                <td class="p-4 text-sm">{{ $item->dob ?? '-' }}</td>
                <td class="p-4 font-bold text-emerald-400">{{ number_format($item->points) }}</td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('admin.customers.edit', $item) }}" class="text-blue-400 hover:text-blue-300"><i class="fa-solid fa-pen-to-square"></i></a>
                    <form action="{{ route('admin.customers.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-4 text-center text-slate-400">Belum ada pelanggan terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection