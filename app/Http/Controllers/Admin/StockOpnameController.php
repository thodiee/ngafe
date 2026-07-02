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
        StockOpname::create($request->all());
        return redirect()->route('admin.stock_opnames.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function show(StockOpname $item) {
        return view('admin.stock_opnames.show', compact('item'));
    }
    public function edit(StockOpname $item) {
        return view('admin.stock_opnames.form', compact('item'));
    }
    public function update(Request $request, StockOpname $item) {
        $item->update($request->all());
        return redirect()->route('admin.stock_opnames.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(StockOpname $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

