<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $data = Supplier::latest()->get();
        return view('admin.suppliers.index', compact('data'));
    }

    public function create()
    {
        return view('admin.suppliers.form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Supplier::create($request->all());
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil ditambahkan');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.form', ['item' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $supplier->update($request->all());
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return back()->with('success', 'Supplier berhasil dihapus');
    }
}
