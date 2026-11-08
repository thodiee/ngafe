<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index() {
        $data = PurchaseOrder::with('supplier')->latest()->get();
        return view('admin.purchase_orders.index', compact('data'));
    }
    public function create() {
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        return view('admin.purchase_orders.form', compact('suppliers'));
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'po_number' => 'required|string|max:50|unique:purchase_orders,po_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'status' => 'required|in:draft,sent,partial,completed,cancelled',
            'total_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);
        PurchaseOrder::create($validated);
        return redirect()->route('admin.purchase_orders.index')->with('success', 'Purchase Order berhasil ditambahkan');
    }
    public function show(PurchaseOrder $item) {
        return redirect()->route('admin.purchase_orders.edit', $item);
    }
    public function edit(PurchaseOrder $item) {
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        return view('admin.purchase_orders.form', compact('item', 'suppliers'));
    }
    public function update(Request $request, PurchaseOrder $item) {
        $validated = $request->validate([
            'po_number' => 'required|string|max:50|unique:purchase_orders,po_number,' . $item->id,
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'status' => 'required|in:draft,sent,partial,completed,cancelled',
            'total_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);
        $item->update($validated);
        return redirect()->route('admin.purchase_orders.index')->with('success', 'Purchase Order berhasil diperbarui');
    }
    public function destroy(PurchaseOrder $item) {
        $item->delete();
        return back()->with('success', 'Purchase Order berhasil dihapus');
    }
}

