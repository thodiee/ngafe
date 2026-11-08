<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Ingredient;
use App\Models\IngredientBatch;
use App\Models\PosSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OwnerReportController extends Controller
{
    public function index(Request $request) {
        $year  = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $month, 1)->endOfMonth();

        // ─── KEUANGAN ──────────────────────────────────────────────
        $grossRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalExpenses = Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');

        // COGS
        $orderItems = OrderItem::whereHas('order', fn($q) =>
            $q->where('payment_status','paid')->whereBetween('created_at', [$startDate, $endDate])
        )->with('product.recipes.ingredient')->get();

        $cogs = 0;
        foreach ($orderItems as $item) {
            if ($item->product && $item->product->recipes) {
                foreach ($item->product->recipes as $recipe) {
                    $avg = IngredientBatch::where('ingredient_id', $recipe->ingredient_id)->avg('unit_price') ?? 0;
                    $cogs += $recipe->quantity * $item->quantity * $avg;
                }
            }
        }

        $grossProfit = $grossRevenue - $cogs;
        $netProfit   = $grossProfit - $totalExpenses;
        $margin      = $grossRevenue > 0 ? round(($netProfit / $grossRevenue) * 100, 1) : 0;

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $dateExpr  = $isSqlite ? "strftime('%Y-%m-%d', created_at)" : "DATE(created_at)";
        $monthExpr = $isSqlite ? "cast(strftime('%m', created_at) as integer)" : "MONTH(created_at)";
        $hourExpr  = $isSqlite ? "cast(strftime('%H', created_at) as integer)" : "HOUR(created_at)";

        // Revenue per hari (untuk chart)
        $dailyRevenue = Order::where('payment_status','paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$dateExpr} as date, SUM(total_amount) as total")
            ->groupBy('date')->orderBy('date')
            ->pluck('total', 'date');

        // Revenue per bulan tahun ini (untuk chart YTD)
        $monthlyRevenue = Order::where('payment_status','paid')
            ->whereYear('created_at', $year)
            ->selectRaw("{$monthExpr} as month, SUM(total_amount) as total")
            ->groupBy('month')->orderBy('month')
            ->pluck('total', 'month');

        // Biaya per kategori
        $expenseByCategory = Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')->orderByDesc('total')->get();

        // ─── PRODUK TERLARIS ────────────────────────────────────────
        $topProducts = OrderItem::whereHas('order', fn($q) =>
            $q->where('payment_status','paid')->whereBetween('created_at', [$startDate, $endDate])
        )->selectRaw('product_id, SUM(quantity) as qty, SUM(price * quantity) as revenue')
         ->groupBy('product_id')->orderByDesc('qty')->take(10)->with('product')->get();

        // Jam tersibuk
        $peakHours = Order::where('payment_status','paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$hourExpr} as hour, COUNT(*) as cnt")
            ->groupBy('hour')->orderByDesc('cnt')->take(3)->get();

        // ─── PEGAWAI ────────────────────────────────────────────────
        $totalEmployees   = User::whereNotIn('role',['admin'])->count();
        $totalAttendances = Attendance::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();
        $lateCount        = Attendance::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status','late')->count();
        $attendanceRate   = $totalEmployees > 0
            ? round(($totalAttendances / ($totalEmployees * $startDate->daysInMonth)) * 100, 1)
            : 0;

        // Pegawai sering terlambat
        $topLate = Attendance::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status','late')
            ->selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')->orderByDesc('cnt')->take(5)->with('user')->get();

        // Payroll summary
        $payrollSummary = Payroll::where('period_month', $month)->where('period_year', $year)
            ->selectRaw('COUNT(*) as total_employees, SUM(net_salary) as total_payroll, AVG(net_salary) as avg_salary')
            ->first();

        // ─── INVENTORI ─────────────────────────────────────────────
        $lowStock = Ingredient::whereRaw('(stock_gudang + stock_dapur) <= minimum_stock')
            ->orderByRaw('(stock_gudang + stock_dapur) ASC')->take(10)->get();
        $lowStockCount = $lowStock->count();

        // ─── POS SESSIONS ──────────────────────────────────────────
        $sessions    = PosSession::with('user')->whereBetween('opening_time', [$startDate, $endDate])->get();
        $totalSessions   = $sessions->count();
        $avgDifference   = $sessions->avg('difference') ?? 0;
        $totalOrders = Order::where('payment_status','paid')->whereBetween('created_at', [$startDate, $endDate])->count();
        $avgOrderValue   = $totalOrders > 0 ? round($grossRevenue / $totalOrders) : 0;

        return view('admin.owner.report', compact(
            'year','month','startDate','endDate',
            // Finance
            'grossRevenue','cogs','grossProfit','totalExpenses','netProfit','margin',
            'dailyRevenue','monthlyRevenue','expenseByCategory',
            // Products
            'topProducts','peakHours',
            // Orders
            'totalOrders','avgOrderValue',
            // Employees
            'totalEmployees','totalAttendances','lateCount','attendanceRate','topLate','payrollSummary',
            // Inventory
            'lowStockCount','lowStock',
            // POS
            'totalSessions','avgDifference'
        ));
    }
}
