<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['table', 'items.product', 'items.variants'])->latest();

        // Search by order reference
        if ($request->has('search') && !empty($request->search)) {
            $query->where('order_reference', 'like', '%' . $request->search . '%');
        }

        // Filter by order status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('order_status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment') && !empty($request->payment)) {
            $query->where('payment_status', $request->payment);
        }

        $orders = $query->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function cancel(Order $order)
    {
        if ($order->order_status === 'cancelled') {
            return back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($order) {
            // Restore BOM / stock ingredients
            foreach ($order->items as $item) {
                $product = Product::with('recipes')->find($item->product_id);
                if ($product) {
                    foreach ($product->recipes as $recipe) {
                        $needed = $recipe->quantity * $item->quantity;
                        $ingredient = Ingredient::find($recipe->ingredient_id);
                        if ($ingredient) {
                            // 1. Increment kitchen stock back
                            $ingredient->increment('stock_dapur', $needed);

                            // 2. Log Stock Mutation of type 'in'
                            StockMutation::create([
                                'ingredient_id' => $ingredient->id,
                                'location' => 'dapur',
                                'type' => 'in',
                                'quantity' => $needed,
                                'reference_type' => 'POSOrderCancel',
                                'reference_id' => $order->id,
                                'notes' => 'Pengembalian stok dari pembatalan pesanan ' . $order->order_reference
                            ]);
                        }
                    }
                }
            }

            // Update order status
            $order->update([
                'order_status' => 'cancelled',
                'payment_status' => 'refunded'
            ]);
        });

        return back()->with('success', 'Pesanan ' . $order->order_reference . ' berhasil dibatalkan. Stok bahan baku (BOM) telah dikembalikan ke dapur. Silakan lakukan pengembalian uang secara tunai kepada pelanggan.');
    }
}
