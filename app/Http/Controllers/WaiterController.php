<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class WaiterController extends Controller
{
    public function index() {
        // Waiter sees orders that are 'ready' to be served
        $orders = Order::where('order_status', 'ready')
                       ->with('table', 'items.product', 'items.variants')
                       ->orderBy('created_at', 'asc')
                       ->get();
                       
        return view('waiter.index', compact('orders'));
    }

    public function markAsServed(Order $order) {
        if ($order->order_status == 'ready') {
            $order->update(['order_status' => 'completed']);
        }
        return redirect()->route('waiter.index')->with('success', 'Pesanan ' . $order->order_reference . ' telah disajikan ke pelanggan.');
    }
}
