<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Waste;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    public function index() {
        $data = Waste::latest()->get();
        return view('admin.wastes.index', compact('data'));
    }
    public function create() {
        return view('admin.wastes.form');
    }
    public function store(Request $request) {
        Waste::create($request->all());
        return redirect()->route('admin.wastes.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function show(Waste $item) {
        return view('admin.wastes.show', compact('item'));
    }
    public function edit(Waste $item) {
        return view('admin.wastes.form', compact('item'));
    }
    public function update(Request $request, Waste $item) {
        $item->update($request->all());
        return redirect()->route('admin.wastes.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(Waste $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

