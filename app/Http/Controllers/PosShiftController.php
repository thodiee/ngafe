<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\PosSession;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PosShiftController extends Controller {
    public function open() {
        $activeSession = PosSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if ($activeSession) {
            return redirect()->route('pos.menu');
        }

        $isAdminOrOwner = in_array(auth()->user()->role, ['admin', 'owner']);

        // Cek Jadwal Shift hari ini
        $schedule = \App\Models\Schedule::with('shift')
            ->where('user_id', auth()->id())
            ->where('date', today()->format('Y-m-d'))
            ->first();

        if (!$schedule || !$schedule->shift) {
            if ($isAdminOrOwner) {
                // Buat dummy schedule untuk admin/owner agar bisa buka kasir
                $dummyShift = new \stdClass();
                $dummyShift->name = 'Bebas (Admin/Owner)';
                $dummyShift->start_time = '00:00:00';
                $dummyShift->end_time = '23:59:59';
                
                $schedule = new \stdClass();
                $schedule->shift = $dummyShift;
            } else {
                return back()->with('error', 'Anda tidak memiliki jadwal shift hari ini.');
            }
        }

        $now = Carbon::now();
        $startTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $schedule->shift->start_time);
        
        // Toleransi buka shift 30 menit sebelum jadwal
        if (!$isAdminOrOwner && $now->lt($startTime->subMinutes(30))) {
            return back()->with('error', 'Belum waktunya buka kasir. Shift Anda dimulai pukul ' . \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i'));
        }

        return view('pos.shift.open', compact('schedule'));
    }

    public function storeOpen(Request $request) {
        $request->validate([
            'opening_cash' => 'required|numeric|min:0'
        ]);

        $activeSession = PosSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if ($activeSession) {
            return redirect()->route('pos.menu');
        }

        $isAdminOrOwner = in_array(auth()->user()->role, ['admin', 'owner']);

        $schedule = \App\Models\Schedule::with('shift')
            ->where('user_id', auth()->id())
            ->where('date', today()->format('Y-m-d'))
            ->first();

        if (!$schedule || !$schedule->shift) {
            if ($isAdminOrOwner) {
                // Buat dummy schedule untuk admin/owner agar bisa buka kasir
                $dummyShift = new \stdClass();
                $dummyShift->name = 'Bebas (Admin/Owner)';
                $dummyShift->start_time = '00:00:00';
                $dummyShift->end_time = '23:59:59';
                
                $schedule = new \stdClass();
                $schedule->shift = $dummyShift;
            } else {
                return back()->with('error', 'Anda tidak memiliki jadwal shift hari ini.');
            }
        }

        $now = Carbon::now();
        $startTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $schedule->shift->start_time);
        
        if (!$isAdminOrOwner && $now->lt($startTime->subMinutes(30))) {
            return back()->with('error', 'Belum waktunya buka kasir. Shift Anda dimulai pukul ' . \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i'));
        }

        PosSession::create([
            'user_id' => auth()->id(),
            'opening_time' => Carbon::now(),
            'opening_cash' => $request->opening_cash,
            'status' => 'open'
        ]);

        return redirect()->route('pos.menu')->with('success', 'Shift kasir berhasil dibuka!');
    }

    public function close() {
        $session = PosSession::where('user_id', auth()->id())->where('status', 'open')->firstOrFail();
        
        $cashSales = $session->orders()->where('payment_status', 'paid')->where('payment_method', 'cash')->sum('total_amount');
        $expectedCash = $session->opening_cash + $cashSales;
        $unfinishedOrders = $session->orders()->whereIn('order_status', ['pending', 'processing', 'ready'])->count();

        return view('pos.shift.close', compact('session', 'expectedCash', 'unfinishedOrders'));
    }

    public function storeClose(Request $request) {
        $request->validate([
            'closing_cash_actual' => 'required|numeric|min:0'
        ]);

        $session = PosSession::where('user_id', auth()->id())->where('status', 'open')->firstOrFail();
        
        // Calculate system cash (only cash payments)
        $cashSales = $session->orders()->where('payment_status', 'paid')->where('payment_method', 'cash')->sum('total_amount');
        $expectedCash = $session->opening_cash + $cashSales;
        
        $difference = $request->closing_cash_actual - $expectedCash;

        $session->update([
            'closing_time' => Carbon::now(),
            'closing_cash_system' => $expectedCash,
            'closing_cash_actual' => $request->closing_cash_actual,
            'difference' => $difference,
            'notes' => $request->notes,
            'status' => 'closed'
        ]);

        return redirect()->route('pos.shift.summary', $session->id)->with('success', 'Shift berhasil ditutup.');
    }

    public function summary($id) {
        $session = PosSession::with('user')->findOrFail($id);
        if ($session->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $totalSales = $session->orders()->where('payment_status', 'paid')->sum('total_amount');
        $cashSales = $session->orders()->where('payment_status', 'paid')->where('payment_method', 'cash')->sum('total_amount');
        $qrisSales = $session->orders()->where('payment_status', 'paid')->where('payment_method', 'qris')->sum('total_amount');

        // Products sold during this session
        $productsSold = OrderItem::whereHas('order', function($q) use ($session) {
            $q->where('pos_session_id', $session->id)->where('payment_status', 'paid');
        })
        ->selectRaw('product_id, sum(quantity) as total_qty, sum(price * quantity) as total_revenue')
        ->groupBy('product_id')
        ->with('product')
        ->get();

        return view('pos.shift.summary', compact('session', 'totalSales', 'cashSales', 'qrisSales', 'productsSold'));
    }
}