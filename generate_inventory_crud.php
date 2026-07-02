<?php
$baseDir = __DIR__ . '/resources/views/admin';

$modules = [
    'purchase_orders' => ['PurchaseOrder', 'po_number', 'supplier_id', 'po_date', 'status', 'total_amount'],
    'goods_receipts' => ['GoodsReceipt', 'gr_number', 'purchase_order_id', 'receipt_date'],
    'stock_opnames' => ['StockOpname', 'opname_number', 'opname_date', 'location', 'status'],
    'recipes' => ['Recipe', 'product_id', 'ingredient_id', 'quantity'],
    'wastes' => ['Waste', 'waste_date', 'location', 'notes'],
    'stock_transfers' => ['StockTransfer', 'transfer_number', 'transfer_date', 'notes'],
];

foreach ($modules as $dir => $fields) {
    $modelName = array_shift($fields);
    $path = $baseDir . '/' . $dir;
    if (!is_dir($path)) mkdir($path, 0777, true);
    
    // index.blade.php
    $index = "@extends('admin.layout')\n@section('content')\n";
    $index .= "<div class=\"flex justify-between items-center mb-6\">\n";
    $index .= "    <h2 class=\"text-2xl font-bold\">Manajemen $modelName</h2>\n";
    $index .= "    <a href=\"{{ route('admin.{$dir}.create') }}\" class=\"bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg transition\"><i class=\"fa-solid fa-plus mr-2\"></i> Tambah $modelName</a>\n";
    $index .= "</div>\n";
    $index .= "<div class=\"bg-slate-800 rounded-xl p-6 border border-slate-700\">\n";
    $index .= "    <table class=\"w-full text-left\">\n";
    $index .= "        <thead>\n";
    $index .= "            <tr class=\"border-b border-slate-700 text-slate-400\">\n";
    foreach($fields as $f) $index .= "                <th class=\"pb-3 font-medium\">" . ucfirst(str_replace('_', ' ', $f)) . "</th>\n";
    $index .= "                <th class=\"pb-3 font-medium\">Aksi</th>\n";
    $index .= "            </tr>\n";
    $index .= "        </thead>\n";
    $index .= "        <tbody>\n";
    $index .= "            @foreach(\$data as \$item)\n";
    $index .= "            <tr class=\"border-b border-slate-700/50\">\n";
    foreach($fields as $f) $index .= "                <td class=\"py-4\">{{ \$item->$f }}</td>\n";
    $index .= "                <td class=\"py-4\">\n";
    $index .= "                    <a href=\"{{ route('admin.{$dir}.show', \$item) }}\" class=\"text-blue-400 hover:text-blue-300 mr-3\"><i class=\"fa-solid fa-eye\"></i> Detail</a>\n";
    $index .= "                    <form action=\"{{ route('admin.{$dir}.destroy', \$item) }}\" method=\"POST\" class=\"inline-block\">\n";
    $index .= "                        @csrf @method('DELETE')\n";
    $index .= "                        <button type=\"submit\" class=\"text-red-400 hover:text-red-300\" onclick=\"return confirm('Yakin ingin menghapus?')\"><i class=\"fa-solid fa-trash\"></i> Hapus</button>\n";
    $index .= "                    </form>\n";
    $index .= "                </td>\n";
    $index .= "            </tr>\n";
    $index .= "            @endforeach\n";
    $index .= "        </tbody>\n";
    $index .= "    </table>\n";
    $index .= "</div>\n";
    $index .= "@endsection\n";
    file_put_contents("$path/index.blade.php", $index);
    // form.blade.php
    $form = "@extends('admin.layout')\n@section('content')\n";
    $form .= "<div class=\"mb-6\">\n";
    $form .= "    <h2 class=\"text-2xl font-bold\">{{ isset(\$item) ? 'Edit' : 'Tambah' }} $modelName</h2>\n";
    $form .= "</div>\n";
    $form .= "<div class=\"bg-slate-800 rounded-xl p-6 border border-slate-700 max-w-2xl\">\n";
    $form .= "    <form action=\"{{ isset(\$item) ? route('admin.{$dir}.update', \$item) : route('admin.{$dir}.store') }}\" method=\"POST\">\n";
    $form .= "        @csrf\n";
    $form .= "        @if(isset(\$item)) @method('PUT') @endif\n";
    foreach($fields as $f) {
        $form .= "        <div class=\"mb-4\">\n";
        $form .= "            <label class=\"block text-slate-400 mb-2\">" . ucfirst(str_replace('_', ' ', $f)) . "</label>\n";
        $form .= "            <input type=\"text\" name=\"$f\" value=\"{{ old('$f', \$item->$f ?? '') }}\" class=\"w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500\" required>\n";
        $form .= "        </div>\n";
    }
    $form .= "        <div class=\"mt-6\">\n";
    $form .= "            <button type=\"submit\" class=\"bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-lg transition\">Simpan</button>\n";
    $form .= "            <a href=\"{{ route('admin.{$dir}.index') }}\" class=\"ml-4 text-slate-400 hover:text-white\">Batal</a>\n";
    $form .= "        </div>\n";
    $form .= "    </form>\n";
    $form .= "</div>\n";
    $form .= "@endsection\n";
    file_put_contents("$path/form.blade.php", $form);
}
echo "All views generated.\n";

