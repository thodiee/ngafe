<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    public function index() {
        $data = GoodsReceipt::latest()->get();
        return view('admin.goods_receipts.index', compact('data'));
    }
    public function create() {
        $purchase_orders = \App\Models\PurchaseOrder::all();
        return view('admin.goods_receipts.form', compact('purchase_orders'));
    }
    public function store(Request $request) {
<<<<<<< HEAD
        $validated = $request->validate([
            'gr_number' => 'required|string|max:50|unique:goods_receipts,gr_number',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);
        GoodsReceipt::create($validated);
=======
        GoodsReceipt::create($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.goods_receipts.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function show(GoodsReceipt $item) {
        return view('admin.goods_receipts.show', compact('item'));
    }
    public function edit(GoodsReceipt $item) {
        $purchase_orders = \App\Models\PurchaseOrder::all();
        return view('admin.goods_receipts.form', compact('item', 'purchase_orders'));
    }
    public function update(Request $request, GoodsReceipt $item) {
<<<<<<< HEAD
        $validated = $request->validate([
            'gr_number' => 'required|string|max:50|unique:goods_receipts,gr_number,' . $item->id,
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);
        $item->update($validated);
=======
        $item->update($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.goods_receipts.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(GoodsReceipt $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

