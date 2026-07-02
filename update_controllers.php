<?php

$dir = __DIR__ . '/app/Http/Controllers';

$controllers = [
    'PosController' => <<<'PHP'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;

class PosController extends Controller
{
    public function pinpad() {
        return view('pos.pinpad');
    }

    public function verifyPin(Request $request) {
        $request->validate(['pin' => 'required|string']);
        $user = User::where('pin', $request->pin)->first();
        if ($user) {
            session(['pos_user_id' => $user->id, 'pos_user_role' => $user->role]);
            return redirect()->route('pos.menu');
        }
        return back()->with('error', 'PIN Salah');
    }

    public function menu() {
        if (!session('pos_user_id')) {
            return redirect()->route('pos.pinpad');
        }
        $products = Product::where('is_available', true)->get();
        $tables = Table::all();
        return view('pos.menu', compact('products', 'tables'));
    }

    public function storeOrder(Request $request) {
        if (!session('pos_user_id')) return redirect()->route('pos.pinpad');
        
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'order_type' => 'required|in:dine_in,takeaway',
            'items' => 'required|array',
            'payment_method' => 'required|in:cash,qris_gateway'
        ]);

        $totalAmount = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $totalAmount += $product->price * $item['quantity'];
        }

        $order = Order::create([
            'order_reference' => 'ORD-' . strtoupper(uniqid()),
            'table_id' => $request->table_id,
            'order_type' => $request->order_type,
            'total_amount' => $totalAmount,
            'payment_method' => $request->payment_method,
            'payment_status' => 'unpaid',
            'order_status' => 'pending'
        ]);

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->price
            ]);
        }

        if ($request->payment_method == 'qris_gateway') {
            // Mocking QRIS gateway logic
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
}
PHP,
    'HrController' => <<<'PHP'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Schedule;
use Carbon\Carbon;

class HrController extends Controller
{
    public function dashboard() {
        $attendances = Attendance::with('user')->get();
        $schedules = Schedule::with(['user', 'shift'])->get();
        return view('hr.dashboard', compact('attendances', 'schedules'));
    }

    public function clockIn(Request $request) {
        $user_id = session('pos_user_id') ?? 1; // Dummy for testing
        Attendance::create([
            'user_id' => $user_id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->format('H:i:s'),
            'status' => 'ontime'
        ]);
        return back()->with('success', 'Clock In Berhasil');
    }

    public function clockOut(Request $request) {
        $user_id = session('pos_user_id') ?? 1;
        $attendance = Attendance::where('user_id', $user_id)->where('date', Carbon::today())->first();
        if ($attendance) {
            $attendance->update(['clock_out' => Carbon::now()->format('H:i:s')]);
            return back()->with('success', 'Clock Out Berhasil');
        }
        return back()->with('error', 'Belum Clock In hari ini');
    }
}
PHP,
    'InventoryController' => <<<'PHP'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Product;

class InventoryController extends Controller
{
    public function index() {
        $ingredients = Ingredient::all();
        $products = Product::with('recipes.ingredient')->get();
        return view('inventory.index', compact('ingredients', 'products'));
    }

    public function updateStock(Request $request) {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'stock' => 'required|integer|min:0'
        ]);

        $ingredient = Ingredient::find($request->ingredient_id);
        $ingredient->update(['stock' => $request->stock]);
        return back()->with('success', 'Stok berhasil diperbarui');
    }
}
PHP,
    'AtsController' => <<<'PHP'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobVacancy;
use App\Models\JobApplication;

class AtsController extends Controller
{
    public function jobs() {
        $jobs = JobVacancy::where('status', 'published')->get();
        return view('careers.jobs', compact('jobs'));
    }

    public function apply(Request $request) {
        $request->validate([
            'job_vacancy_id' => 'required|exists:job_vacancies,id',
            'applicant_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'cv' => 'required|file|mimes:pdf|max:2048'
        ]);

        $cvPath = $request->file('cv')->store('cvs', 'public');

        JobApplication::create([
            'job_vacancy_id' => $request->job_vacancy_id,
            'applicant_name' => $request->applicant_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'cv_path' => $cvPath,
            'status' => 'submitted'
        ]);

        return back()->with('success', 'Lamaran berhasil dikirim');
    }
}
PHP
];

foreach ($controllers as $name => $content) {
    $path = $dir . '/' . $name . '.php';
    file_put_contents($path, $content);
    echo "Updated $name\n";
}

echo "All controllers updated.\n";
