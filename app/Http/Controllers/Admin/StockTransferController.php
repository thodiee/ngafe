<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    public function index() {
        $data = StockTransfer::latest()->get();
        return view('admin.stock_transfers.index', compact('data'));
    }
    public function create() {
        return view('admin.stock_transfers.form');
    }
    public function store(Request $request) {
<<<<<<< HEAD
        $validated = $request->validate([
            'transfer_number' => 'required|string|max:50|unique:stock_transfers,transfer_number',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);
        $validated['user_id'] = auth()->id();
        StockTransfer::create($validated);
=======
        StockTransfer::create($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.stock_transfers.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function show(StockTransfer $item) {
        return view('admin.stock_transfers.show', compact('item'));
    }
    public function edit(StockTransfer $item) {
        return view('admin.stock_transfers.form', compact('item'));
    }
    public function update(Request $request, StockTransfer $item) {
<<<<<<< HEAD
        $validated = $request->validate([
            'transfer_number' => 'required|string|max:50|unique:stock_transfers,transfer_number,' . $item->id,
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);
        $item->update($validated);
=======
        $item->update($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.stock_transfers.index')->with('success', 'Data berhasil diperbarui');
    }
    public function destroy(StockTransfer $item) {
        $item->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}

