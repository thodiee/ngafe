<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Product;

class InventoryController extends Controller
{
    public function index() {
        $ingredients = Ingredient::all();
        $products = Product::with('recipes.ingredient')->get();
        return view('inventory.index', compact('ingredients', 'products'));
    }

    // public function updateStock(Request $request) {
    //     // Deprecated. Stock is now managed via admin modules.
    // }
}