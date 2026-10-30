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
<<<<<<< HEAD
        $validated = $request->validate([
            'opname_number' => 'required|string|max:50|unique:stock_opnames,opname_number',
            'opname_date' => 'required|date',
            'location' => 'required|in:gudang,dapur',
            'status' => 'required|in:draft,completed',
            'notes' => 'nullable|string|max:500'
        ]);
        $validated['user_id'] = auth()->id();
        StockOpname::create($validated);
=======
        StockOpname::create($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.stock_opnames.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function show(StockOpname $item) {
        return view('admin.stock_opnames.show', compact('item'));
    }
    public function edit(StockOpname $item) {
        return view('admin.stock_opnames.form', compact('item'));
    }
    public function update(Request $request, StockOpname $item) {
<<<<<<< HEAD
        $validated = $request->validate([
            'opname_number' => 'required|string|max:50|unique:stock_opnames,opname_number,' . $item->id,
            'opname_date' => 'required|date',
            'location' => 'required|in:gudang,dapur',
            'status' => 'required|in:draft,completed',
            'notes' => 'nullable|string|max:500'
        ]);
        $item->update($validated);
=======
        $item->update($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.stock_opnames.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(StockOpname $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

