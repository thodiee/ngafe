<?php
$content = file_get_contents(__DIR__ . '/app/Http/Controllers/AdminController.php');

$useStatements = "use App\Models\Table;\nuse App\Models\Reservation;\n";
$content = preg_replace('/use App\\\\Models\\\\Ingredient;/', "use App\\Models\\Ingredient;\n" . $useStatements, $content);

$newMethods = <<<'PHP'
    // --- TABLES ---
    public function tables() {
        $tables = Table::all();
        return view('admin.tables', compact('tables'));
    }

    public function storeTable(Request $request) {
        $request->validate([
            'table_number' => 'required|string',
            'area' => 'required|in:indoor,outdoor,vip'
        ]);
        Table::create($request->all());
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function updateTable(Request $request, Table $table) {
        $request->validate([
            'table_number' => 'required|string',
            'area' => 'required|in:indoor,outdoor,vip'
        ]);
        $table->update($request->all());
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil diupdate.');
    }

    public function destroyTable(Table $table) {
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil dihapus.');
    }

    // --- RESERVATIONS ---
    public function reservations() {
        $reservations = Reservation::with('table')->orderBy('reservation_time', 'desc')->get();
        $tables = Table::all();
        return view('admin.reservations', compact('reservations', 'tables'));
    }

    public function storeReservation(Request $request) {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'reservation_time' => 'required|date'
        ]);
        Reservation::create([
            'table_id' => $request->table_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'reservation_time' => $request->reservation_time,
            'status' => 'pending'
        ]);
        return redirect()->route('admin.reservations.index')->with('success', 'Reservasi berhasil ditambahkan.');
    }

    public function updateReservationStatus(Request $request, Reservation $reservation) {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);
        $reservation->update(['status' => $request->status]);
        
        // Update table status logic based on reservation completion could be added here
        
        return redirect()->route('admin.reservations.index')->with('success', 'Status reservasi diperbarui.');
    }

    public function destroyReservation(Reservation $reservation) {
        $reservation->delete();
        return redirect()->route('admin.reservations.index')->with('success', 'Reservasi dihapus.');
    }

PHP;

// Find the last closing brace and insert before it
$content = preg_replace('/}\s*$/', "\n" . $newMethods . "}\n", $content);

file_put_contents(__DIR__ . '/app/Http/Controllers/AdminController.php', $content);
echo "AdminController updated.\n";
