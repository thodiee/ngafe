<?php

$dir = __DIR__ . '/app/Http/Controllers';

$controllers = [
    'AuthController' => <<<'PHP'
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function showLogin() {
        return view('auth.login');
    }
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('pos.menu');
        }
        return back()->withErrors(['email' => 'Invalid email or password'])->withInput();
    }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
PHP,

    'AdminController' => <<<'PHP'
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller {
    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }
            return redirect('/')->with('error', 'Unauthorized Access');
        });
    }

    public function dashboard() {
        $userCount = User::count();
        $productCount = Product::count();
        $attendanceCount = Attendance::whereDate('date', today())->count();
        $lowStock = Ingredient::whereColumn('stock', '<=', 'minimum_stock')->count();
        return view('admin.dashboard', compact('userCount', 'productCount', 'attendanceCount', 'lowStock'));
    }

    // --- USERS ---
    public function users() {
        $users = User::all();
        return view('admin.users', compact('users'));
    }
    public function storeUser(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return back()->with('success', 'User added successfully');
    }
    public function updateUser(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required'
        ]);
        if ($request->password) {
            $validated['password'] = Hash::make($request->password);
        }
        $user->update($validated);
        return back()->with('success', 'User updated successfully');
    }
    public function destroyUser(User $user) {
        $user->delete();
        return back()->with('success', 'User deleted');
    }

    // --- CATEGORIES ---
    public function categories() {
        $categories = Category::all();
        return view('admin.categories', compact('categories'));
    }
    public function storeCategory(Request $request) {
        Category::create($request->validate(['name' => 'required']));
        return back()->with('success', 'Category added');
    }
    public function updateCategory(Request $request, Category $category) {
        $category->update($request->validate(['name' => 'required']));
        return back()->with('success', 'Category updated');
    }
    public function destroyCategory(Category $category) {
        $category->delete();
        return back()->with('success', 'Category deleted');
    }

    // --- PRODUCTS (MENU) ---
    public function products() {
        $products = Product::with('category')->get();
        $categories = Category::all();
        return view('admin.products', compact('products', 'categories'));
    }
    public function storeProduct(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
        ]);
        Product::create($validated);
        return back()->with('success', 'Product added');
    }
    public function updateProduct(Request $request, Product $product) {
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
        ]);
        $product->update($validated);
        return back()->with('success', 'Product updated');
    }
    public function destroyProduct(Product $product) {
        $product->delete();
        return back()->with('success', 'Product deleted');
    }

    // --- INGREDIENTS ---
    public function ingredients() {
        $ingredients = Ingredient::all();
        return view('admin.ingredients', compact('ingredients'));
    }
    public function storeIngredient(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'unit' => 'required',
            'stock' => 'required|integer',
            'minimum_stock' => 'required|integer'
        ]);
        Ingredient::create($validated);
        return back()->with('success', 'Ingredient added');
    }
    public function updateIngredient(Request $request, Ingredient $ingredient) {
        $validated = $request->validate([
            'name' => 'required',
            'unit' => 'required',
            'stock' => 'required|integer',
            'minimum_stock' => 'required|integer'
        ]);
        $ingredient->update($validated);
        return back()->with('success', 'Ingredient updated');
    }
    public function destroyIngredient(Ingredient $ingredient) {
        $ingredient->delete();
        return back()->with('success', 'Ingredient deleted');
    }

    // --- ATTENDANCES ---
    public function attendances() {
        $attendances = Attendance::with('user')->orderBy('date', 'desc')->get();
        return view('admin.attendances', compact('attendances'));
    }
}
PHP,

    'PosController' => <<<'PHP'
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;

class PosController extends Controller
{
    public function menu() {
        $products = Product::where('is_available', true)->get();
        $tables = Table::all();
        return view('pos.menu', compact('products', 'tables'));
    }

    public function storeOrder(Request $request) {
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
        $user_id = auth()->id();
        Attendance::create([
            'user_id' => $user_id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->format('H:i:s'),
            'status' => 'ontime'
        ]);
        return back()->with('success', 'Clock In Berhasil');
    }

    public function clockOut(Request $request) {
        $user_id = auth()->id();
        $attendance = Attendance::where('user_id', $user_id)->where('date', Carbon::today())->first();
        if ($attendance) {
            $attendance->update(['clock_out' => Carbon::now()->format('H:i:s')]);
            return back()->with('success', 'Clock Out Berhasil');
        }
        return back()->with('error', 'Belum Clock In hari ini');
    }
}
PHP
];

foreach ($controllers as $name => $content) {
    file_put_contents($dir . '/' . $name . '.php', $content);
    echo "Updated $name\n";
}
echo "Controllers v2 updated.\n";
