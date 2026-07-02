<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;

class PosController extends Controller
{
    public function menu() {
        $activeSession = \App\Models\PosSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$activeSession) {
            return redirect()->route('pos.shift.open');
        }

        $categories = \App\Models\Category::orderBy('name')->get();
        $products = Product::where('is_available', true)->with('category', 'variants.options')->get();
        $tables = Table::with(['reservations' => function($query) {
            $query->whereDate('reservation_time', today())
                  ->whereIn('status', ['pending', 'confirmed']);
        }])->get();
        
        $customers = \App\Models\Customer::orderBy('name')->get();
        $earn_rate = \App\Models\Setting::get('point_earn_threshold', 10000);
        $redeem_value = \App\Models\Setting::get('point_redeem_value', 100);
        $bday_promo_status = \App\Models\Setting::get('bday_promo_status', '0');
        $bday_promo_message = \App\Models\Setting::get('bday_promo_message', 'Gratis 1 Kopi Susu Aren untuk yang berulang tahun!');

        // Quick Stats
        $activeProductsCount = $products->count();
        $activeOrdersCount = Order::whereIn('order_status', ['processing', 'ready'])->count();
        $completedOrdersCount = Order::where('pos_session_id', $activeSession->id)->where('order_status', 'completed')->count();
        $shiftSales = Order::where('pos_session_id', $activeSession->id)->where('payment_status', 'paid')->sum('total_amount');

        // Unpaid Table Self-Service orders pending at cashier
        $unpaidOrders = Order::where('payment_status', 'unpaid')
            ->where('payment_method', 'cashier')
            ->with(['table', 'items.product', 'items.variants'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pos.menu', compact(
            'categories', 'products', 'tables', 'customers', 
            'earn_rate', 'redeem_value', 'bday_promo_status', 'bday_promo_message', 
            'activeSession', 'activeProductsCount', 'activeOrdersCount', 'completedOrdersCount', 'shiftSales',
            'unpaidOrders'
        ));
    }

    public function storeOrder(Request $request) {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'order_type' => 'required|in:dine_in,takeaway',
            'items' => 'required|array',
            'payment_method' => 'required|in:cash,qris_gateway'
        ]);

        $activeSession = \App\Models\PosSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$activeSession) {
            return redirect()->route('pos.shift.open')->with('error', 'Sesi kasir sudah ditutup. Silakan buka shift baru.');
        }

        $totalAmount = 0;
        $orderItemsData = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $itemTotal = $product->price;
            $variantsData = [];

            if (!empty($item['variants'])) {
                foreach ($item['variants'] as $v) {
                    $itemTotal += $v['price_adjustment'] ?? 0;
                    $variantsData[] = [
                        'variant_name' => $v['variant_name'],
                        'option_name' => $v['option_name'],
                        'price_adjustment' => $v['price_adjustment'] ?? 0,
                    ];
                }
            }

            $totalAmount += $itemTotal * $item['quantity'];
            $orderItemsData[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price, // base price
                'variants' => $variantsData
            ];
        }

        $earn_rate = \App\Models\Setting::get('point_earn_threshold', 10000);
        $redeem_value = \App\Models\Setting::get('point_redeem_value', 100);

        $customer_id = $request->customer_id;
        $points_used = 0;
        $discount_amount = 0;
        $points_earned = 0;

        if ($customer_id) {
            $customer = \App\Models\Customer::find($customer_id);
            if ($customer && $request->has('use_points')) {
                $maxDiscountPossible = $customer->points * $redeem_value;
                if ($maxDiscountPossible > $totalAmount) {
                    $discount_amount = $totalAmount;
                } else {
                    $discount_amount = $maxDiscountPossible;
                }
                $points_used = ceil($discount_amount / $redeem_value);
                $customer->decrement('points', $points_used);
                $totalAmount -= $discount_amount;
            }
            if ($totalAmount > 0) {
                $points_earned = floor($totalAmount / $earn_rate);
                if ($customer) {
                    $customer->increment('points', $points_earned);
                }
            }
        }

        $order = Order::create([
            'order_reference' => 'ORD-' . strtoupper(uniqid()),
            'table_id' => $request->table_id,
            'pos_session_id' => $activeSession->id,
            'customer_id' => $customer_id,
            'order_type' => $request->order_type,
            'total_amount' => $totalAmount,
            'points_earned' => $points_earned,
            'points_used' => $points_used,
            'discount_amount' => $discount_amount,
            'payment_method' => $request->payment_method,
            'payment_status' => 'unpaid',
            'order_status' => 'pending'
        ]);

        foreach ($orderItemsData as $data) {
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'price' => $data['price']
            ]);

            foreach ($data['variants'] as $vData) {
                \App\Models\OrderItemVariant::create([
                    'order_item_id' => $orderItem->id,
                    'variant_name' => $vData['variant_name'],
                    'option_name' => $vData['option_name'],
                    'price_adjustment' => $vData['price_adjustment']
                ]);
            }
        }

        // Deduct stock based on BOM (Resep) & FIFO
        foreach ($orderItemsData as $data) {
            $product = Product::with('recipes')->find($data['product_id']);
            if ($product) {
                foreach ($product->recipes as $recipe) {
                    $needed = $recipe->quantity * $data['quantity'];
                    $ingredient = \App\Models\Ingredient::find($recipe->ingredient_id);
                    
                    if ($ingredient) {
                        // 1. Kurangi dari stock_dapur
                        $ingredient->decrement('stock_dapur', $needed);

                        // 2. Logika FIFO dari ingredient_batches
                        $batches = \App\Models\IngredientBatch::where('ingredient_id', $ingredient->id)
                            ->where('current_quantity', '>', 0)
                            ->orderBy('batch_date', 'asc')
                            ->get();
                        
                        $remainingToDeduct = $needed;
                        foreach ($batches as $batch) {
                            if ($remainingToDeduct <= 0) break;
                            if ($batch->current_quantity >= $remainingToDeduct) {
                                $batch->decrement('current_quantity', $remainingToDeduct);
                                $remainingToDeduct = 0;
                            } else {
                                $remainingToDeduct -= $batch->current_quantity;
                                $batch->update(['current_quantity' => 0]);
                            }
                        }

                        // 3. Catat di StockMutation
                        \App\Models\StockMutation::create([
                            'ingredient_id' => $ingredient->id,
                            'location' => 'dapur',
                            'type' => 'out',
                            'quantity' => $needed,
                            'reference_type' => 'POSOrder',
                            'reference_id' => $order->id,
                            'notes' => 'Penjualan POS ' . $order->order_reference
                        ]);
                    }
                }
            }
        }

        if ($request->payment_method == 'qris_gateway') {
            $order->update(['payment_status' => 'paid', 'order_status' => 'processing']);
            return redirect()->route('pos.receipt', $order->id)->with('success', 'Pembayaran QRIS Berhasil');
        } else {
            $order->update(['payment_status' => 'paid', 'order_status' => 'processing']);
            return redirect()->route('pos.receipt', $order->id)->with('success', 'Pembayaran Tunai Berhasil');
        }
    }

    public function receipt(Order $order) {
        return view('pos.receipt', compact('order'));
    }

    public function callbackQris(Request $request) {
        $order = Order::where('gateway_transaction_id', $request->transaction_id)->first();
        if ($order && $request->status == 'settlement') {
            $order->update(['payment_status' => 'paid', 'order_status' => 'processing']);
        }
        return response()->json(['status' => 'ok']);
    }

    /**
     * Process payment for table self-service order at cashier.
     */
    public function processTablePayment(Request $request, $order_id) {
        $activeSession = \App\Models\PosSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$activeSession) {
            return response()->json(['status' => 'error', 'message' => 'Sesi kasir sudah ditutup. Silakan buka shift baru.'], 403);
        }

        $order = Order::findOrFail($order_id);
        
        $request->validate([
            'payment_method' => 'required|in:cash,qris_gateway'
        ]);

        $order->update([
            'payment_status' => 'paid',
            'order_status' => 'processing', // Send to kitchen
            'payment_method' => $request->payment_method, // Update from 'cashier' to cash/qris_gateway
            'pos_session_id' => $activeSession->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran pesanan meja berhasil dikonfirmasi.',
            'redirect_url' => route('pos.receipt', $order->id)
        ]);
    }

    /**
     * Get live status update of shift metrics and unpaid table orders.
     */
    public function liveStatus() {
        $activeSession = \App\Models\PosSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$activeSession) {
            return response()->json(['status' => 'error', 'message' => 'No active session'], 403);
        }
        
        $activeProductsCount = Product::where('is_available', true)->count();
        $activeOrdersCount = Order::whereIn('order_status', ['processing', 'ready'])->count();
        $completedOrdersCount = Order::where('pos_session_id', $activeSession->id)->where('order_status', 'completed')->count();
        $shiftSales = Order::where('pos_session_id', $activeSession->id)->where('payment_status', 'paid')->sum('total_amount');
        
        $unpaidOrders = Order::where('payment_status', 'unpaid')
            ->where('payment_method', 'cashier')
            ->with(['table', 'items.product', 'items.variants'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $unpaidHtml = view('pos.partials.unpaid_orders', compact('unpaidOrders'))->render();
        
        return response()->json([
            'activeProductsCount' => $activeProductsCount,
            'activeOrdersCount' => $activeOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'shiftSales' => (float)$shiftSales,
            'unpaidOrdersCount' => $unpaidOrders->count(),
            'unpaidOrdersHtml' => $unpaidHtml
        ]);
    }
}