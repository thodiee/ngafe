<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index() {
        $data = Recipe::latest()->get();
        return view('admin.recipes.index', compact('data'));
    }
    public function create() {
        return view('admin.recipes.form');
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.001'
        ]);
        Recipe::create($validated);
        return redirect()->route('admin.recipes.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function show(Recipe $item) {
        return view('admin.recipes.show', compact('item'));
    }
    public function edit(Recipe $item) {
        return view('admin.recipes.form', compact('item'));
    }
    public function update(Request $request, Recipe $item) {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.001'
        ]);
        $item->update($validated);
        return redirect()->route('admin.recipes.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(Recipe $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

