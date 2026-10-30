<?php
$file = __DIR__ . '/resources/views/admin/tables.blade.php';
$content = file_get_contents($file);

// Add edit button next to trash button
$content = preg_replace(
    '/<form action="{{ route\(\'admin\.tables\.destroy\'/',
    '<button onclick="openEditModal({{ $table->id }}, \'{{ addslashes($table->table_number) }}\', \'{{ $table->area_id }}\')" class="text-blue-400 hover:text-blue-300 ml-2"><i class="fa-solid fa-pen-to-square"></i></button>
                    <form action="{{ route(\'admin.tables.destroy\'',
    $content
);

// Add Edit Modal at the end
$editModal = <<<'BLADE'

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Edit Meja</h3>
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

<script>
function openEditModal(id, number, area_id) {
    document.getElementById('editForm').action = '/admin/tables/' + id;
    document.getElementById('edit_table_number').value = number;
    document.getElementById('edit_area_id').value = area_id;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection
BLADE;

$content = str_replace('@endsection', $editModal, $content);

file_put_contents($file, $content);
echo "Tables view updated with edit modal.\n";
