<?php
$content = file_get_contents(__DIR__ . '/app/Http/Controllers/AdminController.php');

$useArea = "use App\Models\Area;\n";
$content = str_replace("use App\Models\Table;", "use App\Models\Table;\n" . $useArea, $content);

$newMethods = <<<'PHP'
    // --- AREAS ---
    public function areas() {
        $areas = Area::all();
        return view('admin.areas', compact('areas'));
    }

    public function storeArea(Request $request) {
        $request->validate(['name' => 'required|string']);
        Area::create($request->all());
        return redirect()->route('admin.areas.index')->with('success', 'Area berhasil ditambahkan.');
    }

    public function updateArea(Request $request, Area $area) {
        $request->validate(['name' => 'required|string']);
        $area->update($request->all());
        return redirect()->route('admin.areas.index')->with('success', 'Area diupdate.');
    }

    public function destroyArea(Area $area) {
        $area->delete();
        return redirect()->route('admin.areas.index')->with('success', 'Area dihapus.');
    }

PHP;

// Find tables method to update it
$content = preg_replace('/public function tables\(\) {.*?return view\(\'admin\.tables\', compact\(\'tables\'\)\);\s*}/s', <<<'PHP'
public function tables() {
        $tables = Table::with('area')->get();
        $areas = Area::all();
        return view('admin.tables', compact('tables', 'areas'));
    }
PHP, $content);

// Find storeTable and updateTable to validate area_id
$content = preg_replace('/\'area\' => \'required\|in:indoor,outdoor,vip\'/', "'area_id' => 'required|exists:areas,id'", $content);

$content = preg_replace('/}\s*$/', "\n" . $newMethods . "}\n", $content);

file_put_contents(__DIR__ . '/app/Http/Controllers/AdminController.php', $content);
echo "AdminController Areas updated.\n";
