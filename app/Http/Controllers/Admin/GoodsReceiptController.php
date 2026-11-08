<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    public function index() {
        $data = GoodsReceipt::with(['purchaseOrder', 'supplier'])->latest()->get();
        return view('admin.goods_receipts.index', compact('data'));
    }
    public function create() {
        $purchase_orders = \App\Models\PurchaseOrder::latest()->get();
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        return view('admin.goods_receipts.form', compact('purchase_orders', 'suppliers'));
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'gr_number' => 'required|string|max:50|unique:goods_receipts,gr_number',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);
        GoodsReceipt::create($validated);
        return redirect()->route('admin.goods_receipts.index')->with('success', 'Penerimaan Barang berhasil dicatat');
    }
    public function show(GoodsReceipt $item) {
        return redirect()->route('admin.goods_receipts.edit', $item);
    }
    public function edit(GoodsReceipt $item) {
        $purchase_orders = \App\Models\PurchaseOrder::latest()->get();
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        return view('admin.goods_receipts.form', compact('item', 'purchase_orders', 'suppliers'));
    }
    public function update(Request $request, GoodsReceipt $item) {
        $validated = $request->validate([
            'gr_number' => 'required|string|max:50|unique:goods_receipts,gr_number,' . $item->id,
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);
        $item->update($validated);
        return redirect()->route('admin.goods_receipts.index')->with('success', 'Penerimaan Barang berhasil diperbarui');
    }
    public function destroy(GoodsReceipt $item) {
        $item->delete();
        return back()->with('success', 'Penerimaan Barang berhasil dihapus');
    }
}

