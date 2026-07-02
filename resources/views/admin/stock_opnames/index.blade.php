@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Manajemen StockOpname</h2>
    <a href="{{ route('admin.stock_opnames.create') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg transition"><i class="fa-solid fa-plus mr-2"></i> Tambah StockOpname</a>
</div>
<div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-slate-700 text-slate-400">
                <th class="pb-3 font-medium">Opname number</th>
                <th class="pb-3 font-medium">Opname date</th>
                <th class="pb-3 font-medium">Location</th>
                <th class="pb-3 font-medium">Status</th>
                <th class="pb-3 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr class="border-b border-slate-700/50">
                <td class="py-4">{{ $item->opname_number }}</td>
                <td class="py-4">{{ $item->opname_date }}</td>
                <td class="py-4">{{ $item->location }}</td>
                <td class="py-4">{{ $item->status }}</td>
                <td class="py-4">
                    <a href="{{ route('admin.stock_opnames.show', $item) }}" class="text-blue-400 hover:text-blue-300 mr-3"><i class="fa-solid fa-eye"></i> Detail</a>
                    <form action="{{ route('admin.stock_opnames.destroy', $item) }}" method="POST" class="inline-block">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300" onclick="return confirm('Yakin ingin menghapus?')"><i class="fa-solid fa-trash"></i> Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
