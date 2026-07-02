<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemVariant;
use App\Models\Ingredient;
use App\Models\IngredientBatch;
use App\Models\StockMutation;
use App\Models\Setting;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SelfServiceController extends Controller
{
    /**
     * Show self-service menu catalog for a specific table.
     */
    public function showMenu($qr_token)
    {
        $table = Table::where('qr_token', $qr_token)->firstOrFail();
        
        // Eager load available products and their variants with options
        $categories = Category::with(['products' => function($q) {
            $q->where('is_available', true)->with('variants.options');
        }])->get();

        return view('self_service.menu', compact('table', 'categories'));
    }

    /**
     * Store self-service customer order.
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'required|string|max:100',
            'items' => 'required|array',
            'payment_method' => 'required|in:cashier,qris'
        ]);

        $table = Table::findOrFail($request->table_id);
        
        // Wrap everything in a database transaction
        return DB::transaction(function () use ($request, $table) {
            $totalAmount = 0;
            $orderItemsData = [];

            // Calculate totals and compile item data
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
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
                    'variants' => $variantsData,
                    'notes' => $item['notes'] ?? null
                ];
            }

            // Calculate tax and service charge based on settings
            $subtotal = $totalAmount;
            $serviceCharge = 0;
            $serviceEnabled = Setting::get('service_charge_enabled', '0') === '1';
            if ($serviceEnabled) {
                $serviceRate = (float) Setting::get('service_charge_rate', 5);
                $serviceCharge = $subtotal * ($serviceRate / 100);
            }
            
            $taxAmount = 0;
            $taxEnabled = Setting::get('tax_enabled', '0') === '1';
            $taxInclusive = Setting::get('tax_inclusive', '0') === '1';
            if ($taxEnabled && !$taxInclusive) {
                $taxRate = (float) Setting::get('tax_rate', 11);
                $taxAmount = ($subtotal + $serviceCharge) * ($taxRate / 100);
            }
            
            $totalAmount = $subtotal + $serviceCharge + $taxAmount;

            // Create Order
            $order = Order::create([
                'order_reference' => 'ORD-' . strtoupper(uniqid()),
                'table_id' => $table->id,
                'customer_id' => null, // null since they are guest/non-member
                'pos_session_id' => null, // null because self-service orders are placed outside shift session initially
                'order_type' => 'dine_in',
                'total_amount' => $totalAmount,
                'points_earned' => 0,
                'points_used' => 0,
                'discount_amount' => 0,
                'payment_method' => $request->payment_method, // cashier or qris
                'payment_status' => 'unpaid',
                'order_status' => 'pending' // start as pending until paid or cashier approved
            ]);

            // Save order items & variants
            foreach ($orderItemsData as $data) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                    'notes' => $data['notes']
                ]);

                foreach ($data['variants'] as $vData) {
                    OrderItemVariant::create([
                        'order_item_id' => $orderItem->id,
                        'variant_name' => $vData['variant_name'],
                        'option_name' => $vData['option_name'],
                        'price_adjustment' => $vData['price_adjustment']
                    ]);
                }
            }

            // Deduct stocks based on BOM (Resep) & FIFO
            foreach ($orderItemsData as $data) {
                $product = Product::with('recipes')->find($data['product_id']);
                if ($product) {
                    foreach ($product->recipes as $recipe) {
                        $needed = $recipe->quantity * $data['quantity'];
                        $ingredient = Ingredient::find($recipe->ingredient_id);
                        
                        if ($ingredient) {
                            // 1. Decrement kitchen stock
                            $ingredient->decrement('stock_dapur', $needed);

                            // 2. FIFO batches
                            $batches = IngredientBatch::where('ingredient_id', $ingredient->id)
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

                            // 3. Stock mutation log
                            StockMutation::create([
                                'ingredient_id' => $ingredient->id,
                                'location' => 'dapur',
                                'type' => 'out',
                                'quantity' => $needed,
                                'reference_type' => 'POSOrder',
                                'reference_id' => $order->id,
                                'notes' => 'Self-Service Order ' . $order->order_reference
                            ]);
                        }
                    }
                }
            }

            // Save customer name to notes or order reference/meta if needed.
            // Let's store customer name inside order gateway_transaction_id temporarily as meta or prepend to order ref if no other columns exist,
            // or we can save it by modifying the order details.
            // Wait, does the order model have a column to identify customer name? No, it has customer_id (relations to customers table).
            // Let's check where the cashier sees notes or order references.
            // Wait! Let's save the customer name inside the Order table. Since we don't have a custom customer name column in the database, we can:
            // 1. Put the customer name into the Order notes? Ah, order items have notes, but the Order table does not have a notes column.
            // Wait, does the Order model or table have any other columns? Let's check `getColumnListing('orders')` output:
            // id, order_reference, table_id, order_type, total_amount, payment_method, payment_status, order_status, gateway_transaction_id, customer_id
            // Ah! We can append the customer name directly to the `order_reference`! For example: `ORD-ABC12345 (Andi)`.
            // That is an extremely smart and simple solution! It will display the customer name everywhere the cashier or kitchen sees the order reference without needing schema modifications!
            // Let's do that! We'll update order_reference to `ORD-... - NAME`.
            $cleanName = strip_tags($request->customer_name);
            $order->update([
                'order_reference' => $order->order_reference . ' - ' . $cleanName
            ]);

            // Process Payment Redirect
            if ($request->payment_method === 'cashier') {
                return response()->json([
                    'status' => 'success',
                    'payment_method' => 'cashier',
                    'redirect_url' => route('self-service.payment-pending', $order->order_reference)
                ]);
            } else {
                // QRIS (Midtrans or Simulation)
                $result = MidtransService::createSnapTransaction($order);
                
                if (isset($result['error'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $result['message']
                    ], 500);
                }

                // Save Snap token as gateway transaction ID
                $order->update([
                    'gateway_transaction_id' => $result['token'] ?? null
                ]);

                return response()->json([
                    'status' => 'success',
                    'payment_method' => 'qris',
                    'redirect_url' => $result['redirect_url']
                ]);
            }
        });
    }

    /**
     * Show mock simulation payment page.
     */
    public function showSimulation($order_reference)
    {
        $order = Order::where('order_reference', $order_reference)->firstOrFail();
        return view('self_service.simulation', compact('order'));
    }

    /**
     * Handle payment success screen.
     */
    public function paymentSuccess($order_reference)
    {
        $order = Order::where('order_reference', $order_reference)->firstOrFail();
        return view('self_service.payment_success', compact('order'));
    }

    /**
     * Handle payment pending (cashier payment) screen.
     */
    public function paymentPending($order_reference)
    {
        $order = Order::where('order_reference', $order_reference)->firstOrFail();
        if (request()->wantsJson()) {
            return response()->json([
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status
            ]);
        }
        return view('self_service.payment_pending', compact('order'));
    }

    /**
     * Handle Midtrans callback notifications (webhook).
     */
    public function midtransCallback(Request $request)
    {
        $serverKey = Setting::get('midtrans_server_key');
        $signatureKey = $request->input('signature_key');
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        
        // Verify signature for security
        $localSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        if ($localSignature !== $signatureKey) {
            Log::warning('Midtrans Webhook: Invalid signature');
            return response()->json(['status' => 'invalid_signature'], 400);
        }

        $order = Order::where('order_reference', $orderId)->first();
        if (!$order) {
            Log::error('Midtrans Webhook: Order not found: ' . $orderId);
            return response()->json(['status' => 'order_not_found'], 404);
        }

        $transactionStatus = $request->input('transaction_status');
        $paymentType = $request->input('payment_type');

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'qris_gateway', // update to qris gateway payment
                'order_status' => 'processing' // send to kitchen
            ]);
            Log::info('Midtrans Webhook: Order paid successfully: ' . $orderId);
        } elseif ($transactionStatus == 'pending') {
            $order->update(['payment_status' => 'unpaid']);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $order->update([
                'payment_status' => 'unpaid',
                'order_status' => 'cancelled'
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
