<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PosSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemVariant;
use App\Models\Product;
use App\Models\Table;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $cashier = User::where('role', 'cashier')->first() ?? User::where('role', 'admin')->first();
        if (!$cashier) {
            return;
        }

        $products = Product::with('variants.options')->where('is_package', false)->get();
        $packages = Product::with('variants.options')->where('is_package', true)->get();
        $tables = Table::all();
        $customers = Customer::all();

        if ($products->isEmpty() || $tables->isEmpty()) {
            return;
        }

        // Combine products and packages for purchasing
        $allPurchasables = $products->concat($packages);

        // 1. Create historical sessions and orders for the last 30 days
        for ($day = 30; $day >= 1; $day--) {
            $sessionDate = $now->copy()->subDays($day);
            
            // Open session
            $openingTime = $sessionDate->copy()->setTime(8, 0, 0);
            $closingTime = $sessionDate->copy()->setTime(22, 0, 0);
            
            $posSession = PosSession::create([
                'user_id' => $cashier->id,
                'opening_time' => $openingTime,
                'closing_time' => $closingTime,
                'opening_cash' => 500000,
                'status' => 'closed',
                'notes' => 'Sesi harian berjalan lancar.',
            ]);

            // Create between 10 to 18 orders per day
            $orderCount = rand(10, 18);
            $sessionTotalRevenue = 0;

            for ($o = 1; $o <= $orderCount; $o++) {
                $orderTime = $openingTime->copy()->addMinutes(rand(10, 800));
                
                // Select table or takeaway
                $isTakeaway = (rand(1, 10) <= 3); // 30% takeaway
                $table = $isTakeaway ? null : $tables->random();
                
                // Select optional customer (40% chance)
                $customer = (rand(1, 10) <= 4 && !$customers->isEmpty()) ? $customers->random() : null;

                // Create Order
                $orderRef = 'TRX-' . $orderTime->format('Ymd') . '-' . str_pad($o, 4, '0', STR_PAD_LEFT);
                
                // Set order and payment status
                $paymentMethod = rand(1, 10) <= 6 ? 'Cash' : (rand(1, 10) <= 8 ? 'QRIS Gateway' : 'Debit Card');
                
                $order = Order::create([
                    'order_reference' => $orderRef,
                    'table_id' => $table ? $table->id : null,
                    'order_type' => $isTakeaway ? 'takeaway' : 'dine_in',
                    'total_amount' => 0, // Will be updated
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'paid',
                    'order_status' => 'completed',
                    'pos_session_id' => $posSession->id,
                    'customer_id' => $customer ? $customer->id : null,
                    'created_at' => $orderTime,
                    'updated_at' => $orderTime,
                ]);

                // Create 1 to 4 items for this order
                $itemCount = rand(1, 4);
                $orderTotal = 0;

                for ($i = 0; $i < $itemCount; $i++) {
                    $product = $allPurchasables->random();
                    $qty = rand(1, 3);
                    $itemPrice = $product->price;

                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'price' => $itemPrice,
                        'notes' => rand(1, 10) <= 2 ? 'Pedas dikit / Less sugar' : null,
                        'created_at' => $orderTime,
                        'updated_at' => $orderTime,
                    ]);

                    $itemVariantTotal = 0;
                    // Add variants if applicable (50% chance for products with variants)
                    if ($product->variants && $product->variants->isNotEmpty() && rand(1, 2) === 1) {
                        foreach ($product->variants as $variant) {
                            $option = $variant->options->random();
                            if ($option) {
                                OrderItemVariant::create([
                                    'order_item_id' => $orderItem->id,
                                    'variant_name' => $variant->name,
                                    'option_name' => $option->name,
                                    'price_adjustment' => $option->price_adjustment,
                                ]);
                                $itemVariantTotal += $option->price_adjustment;
                            }
                        }
                    }

                    $totalItemPrice = ($itemPrice + $itemVariantTotal) * $qty;
                    $orderTotal += $totalItemPrice;
                }

                // Apply loyalty points if customer exists
                $pointsEarned = 0;
                $pointsUsed = 0;
                $discountAmount = 0;

                if ($customer) {
                    // 10% chance use points if they have any
                    if ($customer->points > 200 && rand(1, 10) === 1) {
                        $pointsUsed = min($customer->points, 500);
                        $discountAmount = $pointsUsed * 10; // 1 point = 10 Rp
                        $orderTotal = max(0, $orderTotal - $discountAmount);
                        
                        // Deduct customer points in DB
                        $customer->points -= $pointsUsed;
                        $customer->save();
                    }

                    // Earn points (0.001 * total_amount)
                    $pointsEarned = floor($orderTotal * 0.001);
                    $customer->points += $pointsEarned;
                    $customer->save();
                }

                // Update order total
                $order->update([
                    'total_amount' => $orderTotal,
                    'points_earned' => $pointsEarned,
                    'points_used' => $pointsUsed,
                    'discount_amount' => $discountAmount,
                ]);

                $sessionTotalRevenue += $orderTotal;
            }

            // Close session with actual cash
            $surchargeTotal = $sessionTotalRevenue * 0.005; // estimate surcharge/payment difference
            $posSession->update([
                'closing_cash_system' => 500000 + $sessionTotalRevenue,
                'closing_cash_actual' => 500000 + $sessionTotalRevenue + (rand(0, 1) === 1 ? rand(-10000, 10000) : 0),
                'difference' => rand(0, 5) === 0 ? rand(-5000, 5000) : 0,
            ]);
        }

        // 2. Create ACTIVE / OPEN session for TODAY to allow POS & Kitchen testing
        $todaySession = PosSession::create([
            'user_id' => $cashier->id,
            'opening_time' => $now->copy()->setTime(8, 0, 0),
            'opening_cash' => 500000,
            'status' => 'open',
        ]);

        // Create completed orders for today (earlier today)
        for ($o = 1; $o <= 5; $o++) {
            $orderTime = $now->copy()->subHours(8 - $o);
            $table = $tables->random();

            $order = Order::create([
                'order_reference' => 'TRX-' . $now->format('Ymd') . '-00' . $o,
                'table_id' => $table->id,
                'order_type' => 'dine_in',
                'total_amount' => 0,
                'payment_method' => 'Cash',
                'payment_status' => 'paid',
                'order_status' => 'completed',
                'pos_session_id' => $todaySession->id,
                'created_at' => $orderTime,
                'updated_at' => $orderTime,
            ]);

            $product = $products->random();
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => rand(1, 2),
                'price' => $product->price,
                'created_at' => $orderTime,
            ]);

            $order->update(['total_amount' => $product->price]);
        }

        // Create PENDING orders for TODAY (to display on Kitchen/KDS dashboard)
        for ($o = 6; $o <= 8; $o++) {
            $orderTime = $now->copy()->subMinutes(rand(10, 45));
            // Ensure table is marked as occupied
            $table = $tables[$o % count($tables)];
            $table->update(['status' => 'occupied']);

            $order = Order::create([
                'order_reference' => 'TRX-' . $now->format('Ymd') . '-00' . $o,
                'table_id' => $table->id,
                'order_type' => 'dine_in',
                'total_amount' => 0,
                'payment_method' => 'QRIS Gateway',
                'payment_status' => 'paid',
                'order_status' => 'pending', // Pending in Kitchen!
                'pos_session_id' => $todaySession->id,
                'created_at' => $orderTime,
                'updated_at' => $orderTime,
            ]);

            // Add Food & Coffee
            $foodProduct = $products->where('category_id', Product::where('name', 'Nasi Goreng Spesial')->first()->category_id ?? 1)->random();
            $coffeeProduct = $products->where('category_id', Product::where('name', 'Es Kopi Susu Gula Aren')->first()->category_id ?? 1)->random();

            $oi1 = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $foodProduct->id,
                'quantity' => 1,
                'price' => $foodProduct->price,
                'notes' => 'Sangat pedas',
                'created_at' => $orderTime,
            ]);

            $oi2 = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $coffeeProduct->id,
                'quantity' => rand(1, 2),
                'price' => $coffeeProduct->price,
                'notes' => 'Less sugar',
                'created_at' => $orderTime,
            ]);

            $order->update(['total_amount' => $foodProduct->price + ($coffeeProduct->price * $oi2->quantity)]);
        }

        // Create READY orders for TODAY (to display on Waiter dashboard)
        for ($o = 9; $o <= 10; $o++) {
            $orderTime = $now->copy()->subMinutes(rand(15, 30));
            $table = $tables[($o + 2) % count($tables)];
            $table->update(['status' => 'occupied']);

            $order = Order::create([
                'order_reference' => 'TRX-' . $now->format('Ymd') . '-0' . $o,
                'table_id' => $table->id,
                'order_type' => 'dine_in',
                'total_amount' => 0,
                'payment_method' => 'Cash',
                'payment_status' => 'unpaid', // Customer will pay at cashier later
                'order_status' => 'ready', // Ready to serve by Waiter!
                'pos_session_id' => $todaySession->id,
                'created_at' => $orderTime,
                'updated_at' => $orderTime,
            ]);

            $snackProduct = $products->where('category_id', Product::where('name', 'French Fries Golden')->first()->category_id ?? 1)->random();
            $teaProduct = $products->where('category_id', Product::where('name', 'Classic Iced Tea')->first()->category_id ?? 1)->random();

            $oi1 = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $snackProduct->id,
                'quantity' => 1,
                'price' => $snackProduct->price,
                'created_at' => $orderTime,
            ]);

            $oi2 = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $teaProduct->id,
                'quantity' => 1,
                'price' => $teaProduct->price,
                'created_at' => $orderTime,
            ]);

            $order->update(['total_amount' => $snackProduct->price + $teaProduct->price]);
        }
    }
}
