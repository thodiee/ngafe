<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index() {
        $data = Recipe::with(['product', 'ingredient'])->latest()->get();
        return view('admin.recipes.index', compact('data'));
    }
    public function create() {
        $products = \App\Models\Product::where('is_package', false)->orderBy('name')->get();
        $ingredients = \App\Models\Ingredient::orderBy('name')->get();
        return view('admin.recipes.form', compact('products', 'ingredients'));
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.001'
        ]);
        Recipe::create($validated);
        return redirect()->route('admin.recipes.index')->with('success', 'Resep BOM berhasil ditambahkan');
    }
    public function show(Recipe $item) {
        return redirect()->route('admin.recipes.edit', $item);
    }
    public function edit(Recipe $item) {
        $products = \App\Models\Product::where('is_package', false)->orderBy('name')->get();
        $ingredients = \App\Models\Ingredient::orderBy('name')->get();
        return view('admin.recipes.form', compact('item', 'products', 'ingredients'));
    }
    public function update(Request $request, Recipe $item) {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.001'
        ]);
        $item->update($validated);
        return redirect()->route('admin.recipes.index')->with('success', 'Resep BOM berhasil diperbarui');
    }
    public function destroy(Recipe $item) {
        $item->delete();
        return back()->with('success', 'Resep BOM berhasil dihapus');
    }
}

