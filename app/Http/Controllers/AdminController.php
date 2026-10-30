<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\ProductVariant;
use App\Models\ProductVariantOption;
use App\Models\PackageItem;
use App\Models\Table;
use App\Models\Area;

use App\Models\Reservation;

use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller {
    public function dashboard() {
        $userCount = User::count();
        $productCount = Product::count();
        $attendanceCount = Attendance::whereDate('date', today())->count();
        $lowStock = Ingredient::whereRaw('(stock_gudang + stock_dapur) <= minimum_stock')->count();
        $todayBirthdays = \App\Models\Customer::whereMonth('dob', date('m'))
                                              ->whereDay('dob', date('d'))
                                              ->get();

        return view('admin.dashboard', compact('userCount', 'productCount', 'attendanceCount', 'lowStock', 'todayBirthdays'));
    }

    // --- USERS ---
    public function users() {
        $users = User::paginate(50);
        return view('admin.users', compact('users'));
    }
    public function storeUser(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'base_salary' => 'nullable|numeric',
            'daily_allowance' => 'nullable|numeric',
            'lateness_penalty' => 'nullable|numeric'
        ]);
        $validated['password'] = Hash::make($validated['password']);
        if (!isset($validated['base_salary'])) $validated['base_salary'] = 0;
        if (!isset($validated['daily_allowance'])) $validated['daily_allowance'] = 0;
        if (!isset($validated['lateness_penalty'])) $validated['lateness_penalty'] = 0;
        
        User::create($validated);
        return back()->with('success', 'User added successfully');
    }
    public function updateUser(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required',
            'base_salary' => 'nullable|numeric',
            'daily_allowance' => 'nullable|numeric',
            'lateness_penalty' => 'nullable|numeric'
        ]);
        if ($request->password) {
            $validated['password'] = Hash::make($request->password);
        }
        if (!isset($validated['base_salary'])) $validated['base_salary'] = 0;
        if (!isset($validated['daily_allowance'])) $validated['daily_allowance'] = 0;
        if (!isset($validated['lateness_penalty'])) $validated['lateness_penalty'] = 0;
        
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
            'stock_gudang' => 'required|integer',
            'stock_dapur' => 'nullable|integer',
            'minimum_stock' => 'required|integer'
        ]);
        if (!isset($validated['stock_dapur'])) $validated['stock_dapur'] = 0;
        Ingredient::create($validated);
        return back()->with('success', 'Ingredient added');
    }
    public function updateIngredient(Request $request, Ingredient $ingredient) {
        $validated = $request->validate([
            'name' => 'required',
            'unit' => 'required',
            'stock_gudang' => 'required|integer',
            'stock_dapur' => 'required|integer',
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
        $attendances = Attendance::with('user')->orderBy('date', 'desc')->paginate(50);
        // Pegawai yg sudah daftar wajah tapi belum diverifikasi HR
        $pendingFaceUsers = User::whereNotNull('face_reference')->whereNull('face_verified_at')->get();
        return view('admin.attendances', compact('attendances', 'pendingFaceUsers'));
    }

    public function verifyFace(User $user) {
        $user->face_verified_at = now();
        $user->save();
        return back()->with('success', "Wajah {$user->name} berhasil diverifikasi!");
    }

    public function destroyAttendance(Attendance $attendance) {
        $attendance->delete();
        return back()->with('success', 'Data absensi berhasil dihapus');
    }

    public function destroyAttendanceProof(Attendance $attendance) {
        $attendance->photo_proof = null;
        $attendance->save();
        return back()->with('success', 'Foto bukti absensi berhasil dihapus');
    }

    public function bulkDestroyAttendanceProof(Request $request) {
        $months = (int) $request->input('months', 1);
        $thresholdDate = now()->subMonths($months)->format('Y-m-d');
        
        $attendances = Attendance::whereNotNull('photo_proof')->where('date', '<', $thresholdDate)->get();
        $count = $attendances->count();

        foreach ($attendances as $att) {
            $att->photo_proof = null;
            $att->save();
        }

        return back()->with('success', "Berhasil membersihkan $count foto bukti absensi yang lebih lama dari $months bulan.");
    }

    // --- TABLES ---
    public function tables() {
        $tables = Table::with('area')->get();
        $areas = Area::all();
        return view('admin.tables', compact('tables', 'areas'));
    }

    public function storeTable(Request $request) {
<<<<<<< HEAD
        $validated = $request->validate([
            'table_number' => 'required|string|max:50',
            'area_id' => 'required|exists:areas,id'
        ]);
        Table::create($validated);
=======
        $request->validate([
            'table_number' => 'required|string',
            'area_id' => 'required|exists:areas,id'
        ]);
        Table::create($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function updateTable(Request $request, Table $table) {
<<<<<<< HEAD
        $validated = $request->validate([
            'table_number' => 'required|string|max:50',
            'area_id' => 'required|exists:areas,id'
        ]);
        $table->update($validated);
=======
        $request->validate([
            'table_number' => 'required|string',
            'area_id' => 'required|exists:areas,id'
        ]);
        $table->update($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil diupdate.');
    }

    public function destroyTable(Table $table) {
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil dihapus.');
    }

    // --- RESERVATIONS ---
    public function reservations() {
        $reservations = Reservation::with('table')->orderBy('reservation_time', 'desc')->get();
        $tables = Table::all();
        return view('admin.reservations', compact('reservations', 'tables'));
    }

    public function storeReservation(Request $request) {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'reservation_time' => 'required|date'
        ]);
        Reservation::create([
            'table_id' => $request->table_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'reservation_time' => $request->reservation_time,
            'status' => 'pending'
        ]);
        return redirect()->route('admin.reservations.index')->with('success', 'Reservasi berhasil ditambahkan.');
    }

    public function updateReservationStatus(Request $request, Reservation $reservation) {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);
        $reservation->update(['status' => $request->status]);
        
        // Update table status logic based on reservation completion could be added here
        
        return redirect()->route('admin.reservations.index')->with('success', 'Status reservasi diperbarui.');
    }

    public function destroyReservation(Reservation $reservation) {
        $reservation->delete();
        return redirect()->route('admin.reservations.index')->with('success', 'Reservasi dihapus.');
    }

    // --- AREAS ---
    public function areas() {
        $areas = Area::all();
        return view('admin.areas', compact('areas'));
    }

    public function storeArea(Request $request) {
<<<<<<< HEAD
        $validated = $request->validate(['name' => 'required|string|max:100']);
        Area::create($validated);
=======
        $request->validate(['name' => 'required|string']);
        Area::create($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.areas.index')->with('success', 'Area berhasil ditambahkan.');
    }

    public function updateArea(Request $request, Area $area) {
<<<<<<< HEAD
        $validated = $request->validate(['name' => 'required|string|max:100']);
        $area->update($validated);
=======
        $request->validate(['name' => 'required|string']);
        $area->update($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return redirect()->route('admin.areas.index')->with('success', 'Area diupdate.');
    }

    public function destroyArea(Area $area) {
        $area->delete();
        return redirect()->route('admin.areas.index')->with('success', 'Area dihapus.');
    }

    // --- VARIANTS ---
    public function variants() {
        $products = Product::where('is_package', false)->get();
        $variants = ProductVariant::with(['product', 'options'])->get();
        return view('admin.variants', compact('products', 'variants'));
    }

    public function storeVariant(Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string',
            'is_required' => 'boolean'
        ]);
        ProductVariant::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'is_required' => $request->has('is_required')
        ]);
        return back()->with('success', 'Grup varian berhasil ditambahkan');
    }

    public function updateVariant(Request $request, ProductVariant $variant) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string',
            'is_required' => 'boolean'
        ]);
        $variant->update([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'is_required' => $request->has('is_required')
        ]);
        return back()->with('success', 'Grup varian berhasil diupdate');
    }

    public function destroyVariant(ProductVariant $variant) {
        $variant->delete();
        return back()->with('success', 'Grup varian dihapus');
    }

    public function storeVariantOption(Request $request) {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'name' => 'required|string',
            'price_adjustment' => 'numeric'
        ]);
        ProductVariantOption::create([
            'product_variant_id' => $request->product_variant_id,
            'name' => $request->name,
            'price_adjustment' => $request->price_adjustment ?? 0
        ]);
        return back()->with('success', 'Opsi varian ditambahkan');
    }

    public function destroyVariantOption(ProductVariantOption $option) {
        $option->delete();
        return back()->with('success', 'Opsi varian dihapus');
    }

    // --- PACKAGES ---
    public function packages() {
        $packages = Product::where('is_package', true)->with('packageItems.product')->get();
        $categories = Category::all();
        $products = Product::where('is_package', false)->get();
        return view('admin.packages', compact('packages', 'categories', 'products'));
    }

    public function storePackage(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
        ]);
        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'is_package' => true
        ]);
        return back()->with('success', 'Paket menu dibuat');
    }

    public function storePackageItem(Request $request) {
<<<<<<< HEAD
        $validated = $request->validate([
            'package_id' => 'required|exists:products,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100'
        ]);
        PackageItem::create($validated);
=======
        $request->validate([
            'package_id' => 'required|exists:products,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        PackageItem::create($request->all());
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        return back()->with('success', 'Produk ditambahkan ke paket');
    }

    public function destroyPackageItem(PackageItem $item) {
        $item->delete();
        return back()->with('success', 'Produk dihapus dari paket');
    }
}
