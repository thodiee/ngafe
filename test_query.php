<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\IngredientBatch;

function showStats($month, $year) {
    $startDate = Carbon\Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = Carbon\Carbon::create($year, $month, 1)->endOfMonth();

    $grossRevenue = Order::where('payment_status', 'paid')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('total_amount');

    $totalExpenses = Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->sum('amount');

    // COGS with package fix
    $orderItems = OrderItem::whereHas('order', fn($q) =>
        $q->where('payment_status','paid')->whereBetween('created_at', [$startDate, $endDate])
    )->with('product.recipes', 'product.packageItems.product.recipes')->get();

    $cogs = 0;
    foreach ($orderItems as $item) {
        if (!$item->product) continue;
        if ($item->product->is_package) {
            foreach ($item->product->packageItems as $pItem) {
                if ($pItem->product && $pItem->product->recipes) {
                    foreach ($pItem->product->recipes as $recipe) {
                        $avg = IngredientBatch::where('ingredient_id', $recipe->ingredient_id)->avg('unit_price') ?? 0;
                        $cogs += $item->quantity * $pItem->quantity * $recipe->quantity * $avg;
                    }
                }
            }
        } else {
            if ($item->product->recipes) {
                foreach ($item->product->recipes as $recipe) {
                    $avg = IngredientBatch::where('ingredient_id', $recipe->ingredient_id)->avg('unit_price') ?? 0;
                    $cogs += $recipe->quantity * $item->quantity * $avg;
                }
            }
        }
    }

    $payroll = Payroll::where('period_month', str_pad($month, 2, '0', STR_PAD_LEFT))
        ->where('period_year', $year)
        ->sum('net_salary');

    echo "Stats for $year-$month:\n";
    echo "  Gross Revenue: " . $grossRevenue . "\n";
    echo "  COGS (HPP): " . $cogs . "\n";
    echo "  Operational Expenses: " . $totalExpenses . "\n";
    echo "  Payroll Expenses: " . $payroll . "\n";
    echo "  Total Expenses (Calculated): " . ($cogs + $totalExpenses + $payroll) . "\n";
    echo "  Net Profit (Calculated): " . ($grossRevenue - ($cogs + $totalExpenses + $payroll)) . "\n\n";
}

showStats(5, 2026);
showStats(6, 2026);
