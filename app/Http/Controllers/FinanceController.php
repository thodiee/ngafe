<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Expense;
use App\Models\OrderItem;
use App\Models\IngredientBatch;
use Carbon\Carbon;

class FinanceController extends Controller {
    public function dashboard(Request $request) {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        $orders = Order::whereMonth('created_at', $month)
                       ->whereYear('created_at', $year)
                       ->where('payment_status', 'paid')
                       ->get();

        $grossRevenue = $orders->sum('total_amount');
        $cogs = 0;

        // Estimated COGS calculation based on items sold
        $orderItems = OrderItem::whereHas('order', function($q) use ($month, $year) {
            $q->whereMonth('created_at', $month)
              ->whereYear('created_at', $year)
              ->where('payment_status', 'paid');
        })->with('product.recipes.ingredient')->get();

        foreach ($orderItems as $item) {
            if ($item->product && $item->product->recipes) {
                foreach ($item->product->recipes as $recipe) {
                    $qtyNeeded = $recipe->quantity * $item->quantity;
                    // Get average unit price of ingredient
                    $avgPrice = IngredientBatch::where('ingredient_id', $recipe->ingredient_id)->avg('unit_price') ?? 0;
                    $cogs += ($qtyNeeded * $avgPrice);
                }
            }
        }

        $grossProfit = $grossRevenue - $cogs;

        $expenses = Expense::whereMonth('expense_date', $month)
                           ->whereYear('expense_date', $year)
                           ->sum('amount');
                           
        $netProfit = $grossProfit - $expenses;

        // Best selling products
        $bestSellers = OrderItem::whereHas('order', function($q) use ($month, $year) {
                            $q->whereMonth('created_at', $month)
                              ->whereYear('created_at', $year)
                              ->where('payment_status', 'paid');
                        })
                        ->selectRaw('product_id, sum(quantity) as total_qty, sum(price * quantity) as total_revenue')
                        ->groupBy('product_id')
                        ->orderBy('total_qty', 'desc')
                        ->take(5)
                        ->with('product')
                        ->get();

        return view('admin.finance.dashboard', compact('month', 'year', 'grossRevenue', 'cogs', 'grossProfit', 'expenses', 'netProfit', 'bestSellers'));
    }

    public function expenses(Request $request) {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        
        $expenses = Expense::whereMonth('expense_date', $month)
                           ->whereYear('expense_date', $year)
                           ->orderBy('expense_date', 'desc')
                           ->get();
                           
        return view('admin.finance.expenses', compact('expenses', 'month', 'year'));
    }

    public function storeExpense(Request $request) {
        $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        Expense::create([
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function report(Request $request) {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        $orders = Order::whereMonth('created_at', $month)
                       ->whereYear('created_at', $year)
                       ->where('payment_status', 'paid')
                       ->get();

        $grossRevenue = $orders->sum('total_amount');
        $cogs = 0;

        $orderItems = OrderItem::whereHas('order', function($q) use ($month, $year) {
            $q->whereMonth('created_at', $month)
              ->whereYear('created_at', $year)
              ->where('payment_status', 'paid');
        })->with('product.recipes')->get();

        foreach ($orderItems as $item) {
            if ($item->product && $item->product->recipes) {
                foreach ($item->product->recipes as $recipe) {
                    $qtyNeeded = $recipe->quantity * $item->quantity;
                    $avgPrice = IngredientBatch::where('ingredient_id', $recipe->ingredient_id)->avg('unit_price') ?? 0;
                    $cogs += ($qtyNeeded * $avgPrice);
                }
            }
        }

        $grossProfit = $grossRevenue - $cogs;

        // Group expenses by category
        $expenseCategories = Expense::whereMonth('expense_date', $month)
                                    ->whereYear('expense_date', $year)
                                    ->selectRaw('category, sum(amount) as total')
                                    ->groupBy('category')
                                    ->get();
                                    
        $totalExpenses = $expenseCategories->sum('total');
        $netProfit = $grossProfit - $totalExpenses;

        return view('admin.finance.report', compact('month', 'year', 'grossRevenue', 'cogs', 'grossProfit', 'expenseCategories', 'totalExpenses', 'netProfit'));
    }
}