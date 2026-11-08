<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\StockMutation;
use Illuminate\Http\Request;

class StockMutationController extends Controller
{
    public function index() {
        $data = StockMutation::with('ingredient')->latest()->get();
        return view('admin.stock_mutations.index', compact('data'));
    }
    public function create() {
        return view('admin.stock_mutations.form');
    }
    public function store(Request $request) {
        StockMutation::create($request->all());
        return redirect()->route('admin.stock_mutations.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function show(StockMutation $item) {
        return view('admin.stock_mutations.show', compact('item'));
    }
    public function edit(StockMutation $item) {
        return view('admin.stock_mutations.form', compact('item'));
    }
    public function update(Request $request, StockMutation $item) {
        $item->update($request->all());
        return redirect()->route('admin.stock_mutations.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(StockMutation $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

