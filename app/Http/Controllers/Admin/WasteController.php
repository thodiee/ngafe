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
        $validated = $request->validate([
            'waste_date' => 'required|date',
            'location' => 'required|in:gudang,dapur',
            'notes' => 'nullable|string|max:500'
        ]);
        $validated['user_id'] = auth()->id();
        Waste::create($validated);
        return redirect()->route('admin.wastes.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function show(Waste $item) {
        return view('admin.wastes.show', compact('item'));
    }
    public function edit(Waste $item) {
        return view('admin.wastes.form', compact('item'));
    }
    public function update(Request $request, Waste $item) {
        $validated = $request->validate([
            'waste_date' => 'required|date',
            'location' => 'required|in:gudang,dapur',
            'notes' => 'nullable|string|max:500'
        ]);
        $item->update($validated);
        return redirect()->route('admin.wastes.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(Waste $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

