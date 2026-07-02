<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index() {
        $data = PurchaseOrder::latest()->get();
        return view('admin.purchase_orders.index', compact('data'));
    }
    public function create() {
        $suppliers = \App\Models\Supplier::all();
        return view('admin.purchase_orders.form', compact('suppliers'));
    }
    public function store(Request $request) {
        PurchaseOrder::create($request->all());
        return redirect()->route('admin.purchase_orders.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function show(PurchaseOrder $item) {
        return view('admin.purchase_orders.show', compact('item'));
    }
    public function edit(PurchaseOrder $item) {
        $suppliers = \App\Models\Supplier::all();
        return view('admin.purchase_orders.form', compact('item', 'suppliers'));
    }
    public function update(Request $request, PurchaseOrder $item) {
        $item->update($request->all());
        return redirect()->route('admin.purchase_orders.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(PurchaseOrder $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

