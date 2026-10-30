<?php
$file = __DIR__ . '/resources/views/admin/areas.blade.php';
$content = file_get_contents($file);

// Add edit button next to trash button
$content = preg_replace(
    '/<form action="{{ route\(\'admin\.areas\.destroy\'/',
    '<button onclick="openEditModal({{ $area->id }}, \'{{ addslashes($area->name) }}\')" class="text-blue-400 hover:text-blue-300 ml-2"><i class="fa-solid fa-pen-to-square"></i></button>
                    <form action="{{ route(\'admin.areas.destroy\'',
    $content
);

// Add Edit Modal at the end
$editModal = <<<'BLADE'

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex p-4">
    <div class="bg-slate-800 p-6 rounded-2xl w-full max-w-md border border-slate-700 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Edit Area</h3>
        <form id="editForm" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm mb-1 text-slate-400">Nama Area</label>
                <input type="text" id="edit_name" name="name" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 bg-slate-700 rounded text-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-500">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name) {
    document.getElementById('editForm').action = '/admin/areas/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection
BLADE;

$content = str_replace('@endsection', $editModal, $content);

file_put_contents($file, $content);
echo "Areas view updated with edit modal.\n";
