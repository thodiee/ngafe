<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class KitchenController extends Controller
{
    public function index() {
        // Get all orders with status 'processing' (which means paid/sent to kitchen)
        $orders = Order::where('order_status', 'processing')
                       ->with('table', 'items.product', 'items.variants')
                       ->orderBy('created_at', 'asc')
                       ->get();
                       
        return view('kitchen.index', compact('orders'));
    }

    public function markAsReady(Order $order) {
        if ($order->order_status == 'processing') {
            $order->update(['order_status' => 'ready']);
        }
        return redirect()->route('kitchen.index')->with('success', 'Pesanan ' . $order->order_reference . ' ditandai selesai.');
    }
}