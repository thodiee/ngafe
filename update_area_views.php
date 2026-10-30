<?php
$viewsDir = __DIR__ . '/resources/views/admin';

$views = [
    'areas.blade.php' => <<<'BLADE'
@extends('admin.layout')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Master Area Meja</h2>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold transition">Tambah Area</button>
</div>

<div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg">
    <table class="w-full text-left">
        <thead class="bg-slate-900 border-b border-slate-700 text-slate-400">
            <tr>
                <th class="p-4">Nama Area</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($areas as $area)
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="p-4 font-bold">{{ $area->name }}</td>
                <td class="p-4 text-right">
                    <form action="{{ route('admin.areas.destroy', $area->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus area ini? (Meja pada area ini akan menjadi tanpa area)')">
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
        <h3 class="text-xl font-bold mb-4">Tambah Area Baru</h3>
        <form action="{{ route('admin.areas.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Nama Area (Misal: Lantai 1, Outdoor Belakang)</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE
];

foreach ($views as $name => $content) {
    file_put_contents($viewsDir . '/' . $name, $content);
}

// Update admin/tables.blade.php
$tablesPath = $viewsDir . '/tables.blade.php';
$tablesContent = file_get_contents($tablesPath);

$tablesContent = preg_replace(
    "/<td class=\"p-4\">\s*@if\(\\$table->area === 'indoor'\).*?@endif\s*<\/td>/s",
    "<td class=\"p-4\">\n                    <span class=\"bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs font-bold uppercase\">{{ \$table->area->name ?? 'Tanpa Area' }}</span>\n                </td>",
    $tablesContent
);

$tablesContent = preg_replace(
    "/<select name=\"area\".*?<\/select>/s",
    "<select name=\"area_id\" class=\"w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white\" required>\n                    @foreach(\$areas as \$a)\n                        <option value=\"{{ \$a->id }}\">{{ \$a->name }}</option>\n                    @endforeach\n                </select>",
    $tablesContent
);

file_put_contents($tablesPath, $tablesContent);

echo "Views updated.\n";
