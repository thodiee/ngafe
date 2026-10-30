<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class SalesReportController extends Controller {
    public function index(Request $request) {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        $productsSold = OrderItem::whereHas('order', function($q) use ($month, $year) {
            $q->whereMonth('created_at', $month)
              ->whereYear('created_at', $year)
              ->where('payment_status', 'paid');
        })
        ->selectRaw('product_id, sum(quantity) as total_qty, sum(price * quantity) as total_revenue')
        ->groupBy('product_id')
        ->orderBy('total_qty', 'desc')
        ->with('product')
        ->get();

        $totalItems = $productsSold->sum('total_qty');
        $totalRevenue = $productsSold->sum('total_revenue');

        return view('admin.pos.sales_report', compact('productsSold', 'month', 'year', 'totalItems', 'totalRevenue'));
    }
}