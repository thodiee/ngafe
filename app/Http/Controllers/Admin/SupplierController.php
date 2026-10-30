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
<<<<<<< HEAD
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500'
        ]);
        Supplier::create($validated);
=======
        $request->validate(['name' => 'required|string|max:255']);
        Supplier::create($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil ditambahkan');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.form', ['item' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier)
    {
<<<<<<< HEAD
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500'
        ]);
        $supplier->update($validated);
=======
        $request->validate(['name' => 'required|string|max:255']);
        $supplier->update($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return back()->with('success', 'Supplier berhasil dihapus');
    }
}
