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
<<<<<<< HEAD
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.001'
        ]);
        Recipe::create($validated);
=======
        Recipe::create($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.recipes.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function show(Recipe $item) {
        return view('admin.recipes.show', compact('item'));
    }
    public function edit(Recipe $item) {
        return view('admin.recipes.form', compact('item'));
    }
    public function update(Request $request, Recipe $item) {
<<<<<<< HEAD
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.001'
        ]);
        $item->update($validated);
=======
        $item->update($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.recipes.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(Recipe $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

