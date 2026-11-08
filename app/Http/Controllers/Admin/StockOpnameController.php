<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\StockOpname;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index() {
        $data = StockOpname::latest()->get();
        return view('admin.stock_opnames.index', compact('data'));
    }
    public function create() {
        return view('admin.stock_opnames.form');
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'opname_number' => 'required|string|max:50|unique:stock_opnames,opname_number',
            'opname_date' => 'required|date',
            'location' => 'required|in:gudang,dapur',
            'status' => 'required|in:draft,completed',
            'notes' => 'nullable|string|max:500'
        ]);
        $validated['user_id'] = auth()->id();
        StockOpname::create($validated);
        return redirect()->route('admin.stock_opnames.index')->with('success', 'Stok Opname berhasil ditambahkan');
    }
    public function show(StockOpname $item) {
        return redirect()->route('admin.stock_opnames.edit', $item);
    }
    public function edit(StockOpname $item) {
        return view('admin.stock_opnames.form', compact('item'));
    }
    public function update(Request $request, StockOpname $item) {
        $validated = $request->validate([
            'opname_number' => 'required|string|max:50|unique:stock_opnames,opname_number,' . $item->id,
            'opname_date' => 'required|date',
            'location' => 'required|in:gudang,dapur',
            'status' => 'required|in:draft,completed',
            'notes' => 'nullable|string|max:500'
        ]);
        $item->update($validated);
        return redirect()->route('admin.stock_opnames.index')->with('success', 'Stok Opname berhasil diperbarui');
    }
    public function destroy(StockOpname $item) {
        $item->delete();
        return back()->with('success', 'Stok Opname berhasil dihapus');
    }
}

