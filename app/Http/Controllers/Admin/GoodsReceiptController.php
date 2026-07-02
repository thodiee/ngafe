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
        GoodsReceipt::create($request->all());
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
        $item->update($request->all());
        return redirect()->route('admin.goods_receipts.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(GoodsReceipt $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

