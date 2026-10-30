markdown_content = """# Blueprint Sistem Manajemen F&B (Single Outlet)
## Laravel 10, Tailwind CSS, & MySQL

Dokumen ini berisi rancangan struktur basis data (migrasi), konfigurasi routing, komponen logika (Service & Controller), serta arsitektur antarmuka menggunakan Tailwind CSS untuk sistem manajemen F&B terintegrasi.

---

## 1. Arsitektur Basis Data (Laravel Migrations & MySQL)

Berikut adalah rancangan tabel MySQL yang dikonfigurasi melalui skema migrasi Laravel 10 untuk mendukung relasi antar-modul.

### A. Modul HR, Shift, dan Absensi PIN

Output kode
Traceback (most recent call last):
  File "<xbox-string>", line 480
    markdown_content = """# Blueprint Sistem Manajemen F&B (Single Outlet)
## Laravel 10, Tailwind CSS, & MySQL

Dokumen ini berisi rancangan struktur basis data (migrasi), konfigurasi routing, komponen logika (Service & Controller), serta arsitektur antarmuka menggunakan Tailwind CSS untuk sistem manajemen F&B terintegrasi.

---

## 1. Arsitektur Basis Data (Laravel Migrations & MySQL)

Berikut adalah rancangan tabel MySQL yang dikonfigurasi melalui skema migrasi Laravel 10 untuk mendukung relasi antar-modul.

### A. Modul HR, Shift, dan Absensi PIN

```php
// database/migrations/2026_01_01_000001_create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('pin', 64)->unique(); // Hashed PIN untuk absensi POS
    $table->enum('role', ['owner', 'admin', 'cashier', 'kitchen']);
    $table->rememberToken();
    $table->timestamps();
});

// database/migrations/2026_01_01_000002_create_shifts_table.php
Schema::create('shifts', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // e.g., Morning, Night
    $table->time('start_time');
    $table->time('end_time');
    $table->timestamps();
});

// database/migrations/2026_01_01_000003_create_schedules_table.php
Schema::create('schedules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('shift_id')->constrained()->onDelete('cascade');
    $table->date('date');
    $table->timestamps();
});

// database/migrations/2026_01_01_000004_create_attendances_table.php
Schema::create('attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->date('date');
    $table->time('clock_in');
    $table->time('clock_out')->nullable();
    $table->enum('status', ['ontime', 'late', 'absent']);
    $table->timestamps();
});
B. Modul Menu, Inventaris, & Bill of Materials (BOM)
PHP
// database/migrations/2026_01_01_000005_create_products_table.php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('category'); // e.g., Food, Beverage, Coffee
    $table->decimal('price', 10, 2);
    $table->string('image')->nullable();
    $table->boolean('is_available')->default(true);
    $table->timestamps();
});

// database/migrations/2026_01_01_000006_create_ingredients_table.php
Schema::create('ingredients', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->integer('stock')->default(0);
    $table->string('unit'); // e.g., gram, ml, pcs
    $table->integer('minimum_stock')->default(10);
    $table->timestamps();
});

// database/migrations/2026_01_01_000007_create_recipes_table.php (Bill of Materials)
Schema::create('recipes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
    $table->integer('quantity'); // Jumlah bahan baku yang digunakan per porsi
    $table->timestamps();
});
C. Modul Meja, Transaksi POS, & Self-Ordering QR
PHP
// database/migrations/2026_01_01_000008_create_tables_table.php
Schema::create('tables', function (Blueprint $table) {
    $table->id();
    $table->string('table_number', 10);
    $table->string('qr_token')->unique(); // Token pengaman untuk URL Scan QR
    $table->enum('status', ['empty', 'occupied'])->default('empty');
    $table->timestamps();
});

// database/migrations/2026_01_01_000009_create_orders_table.php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_reference', 50)->unique(); // Invoice ID
    $table->foreignId('table_id')->nullable()->constrained();
    $table->enum('order_type', ['dine_in', 'takeaway']);
    $table->decimal('total_amount', 10, 2);
    $table->enum('payment_method', ['cash', 'qris_gateway'])->nullable();
    $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid');
    $table->enum('order_status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
    $table->string('gateway_transaction_id')->nullable(); // Untuk Midtrans/Xendit callback
    $table->timestamps();
});

// database/migrations/2026_01_01_000010_create_order_items_table.php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained();
    $table->integer('quantity');
    $table->decimal('price', 10, 2); // Harga saat dibeli
    $table->timestamps();
});
D. Modul Rekrutmen (ATS)
PHP
// database/migrations/2026_01_01_000011_create_job_vacancies_table.php
Schema::create('job_vacancies', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->text('requirements');
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->timestamps();
});

// database/migrations/2026_01_01_000012_create_job_applications_table.php
Schema::create('job_applications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_vacancy_id')->constrained()->onDelete('cascade');
    $table->string('applicant_name');
    $table->string('email');
    $table->string('phone_number');
    $table->string('cv_path'); // Path file privat di secured storage
    $table->enum('status', ['submitted', 'screening', 'interview', 'accepted', 'rejected'])->default('submitted');
    $table->timestamps();
});
2. Struktur Routing Sistem (routes/)
Pembagian rute dipisahkan secara modular untuk mengamankan hak akses data internal dan eksternal.

A. Rute Web (routes/web.php)
PHP
use App\Http\Controllers\Front\ComproController;
use App\Http\Controllers\Front\SelfOrderController;
use App\Http\Controllers\POS\TerminalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HRController;

// --- 1. FRONT-END / COMPANY PROFILE (Akses Publik) ---
Route::get('/', [ComproController::class, 'index'])->name('compro.home');
Route::get('/menu', [ComproController::class, 'menu'])->name('compro.menu');
Route::get('/careers', [ComproController::class, 'careers'])->name('compro.careers');
Route::post('/careers/apply', [ComproController::class, 'apply'])->name('compro.apply');

// --- 2. DINE-IN SELF ORDERING (Akses via QR Code Meja) ---
Route::group(['prefix' => 'table-order', 'middleware' => 'validate.table.token'], function () {
    Route::get('/', [SelfOrderController::class, 'index'])->name('self_order.index');
    Route::post('/checkout', [SelfOrderController::class, 'checkout'])->name('self_order.checkout');
});

// --- 3. TERMINAL POS & ABSENSI (Akses Tablet / Staf Restoran) ---
Route::group(['prefix' => 'pos', 'middleware' => 'auth.pos'], function () {
    Route::get('/', [TerminalController::class, 'index'])->name('pos.terminal');
    Route::post('/attendance/verify', [TerminalController::class, 'verifyPin'])->name('pos.attendance.pin');
    Route::post('/order/store', [TerminalController::class, 'storeOrder'])->name('pos.order.store');
});

// --- 4. BACK OFFICE / ADMIN PANEL (Akses Owner & Admin/HR) ---
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:owner,admin']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Manajemen HR & Penjadwalan
    Route::get('/hr/schedules', [HRController::class, 'schedules'])->name('admin.hr.schedules');
    Route::get('/hr/attendances', [HRController::class, 'attendances'])->name('admin.hr.attendances');
    Route::get('/hr/applicants', [HRController::class, 'applicants'])->name('admin.hr.applicants');
    Route::patch('/hr/applicants/{id}/status', [HRController::class, 'updateApplicantStatus'])->name('admin.hr.applicants.update');
    
    // Inventaris & Gudang
    Route::resource('ingredients', \App\Http\Controllers\Admin\IngredientController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
});
B. Rute API (routes/api.php)
PHP
use App\Http\Controllers\API\PaymentGatewayCallbackController;

// Webhook Handler untuk Payment Gateway (Midtrans / Xendit)
Route::post('/payment/callback', [PaymentGatewayCallbackController::class, 'handleCallback']);
3. Komponen Logika Inti & Integrasi Pihak Ketiga
A. Fonnte WhatsApp Service (app/Services/FonnteService.php)
Fungsi mandiri memanfaatkan HTTP Client Laravel untuk mengirimkan notifikasi asinkronus ke WhatsApp.

PHP
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService 
{
    /**
     * Kirim Pesan Teks via Fonnte API
     */
    public static function sendMessage(string $target, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])->timeout(10)->post('[https://api.fonnte.com/send](https://api.fonnte.com/send)', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62'
            ]);

            return $response->json();
        } catch (\\Exception $e) {
            Log::error("Fonnte API Error: " . $e->getMessage());
            return ['status' => false, 'reason' => $e->getMessage()];
        }
    }
}
B. Background Queue Job (app/Jobs/SendWANotification.php)
PHP
namespace App\Jobs;

use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWANotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $phoneNumber, 
        protected string $message
    ) {}

    public function handle(): void
    {
        FonnteService::sendMessage($this->phoneNumber, $this->message);
    }
}
C. Webhook Payment Handler (app/Http/Controllers/API/PaymentGatewayCallbackController.php)
Menangani pembaruan status pemesanan instan saat pelanggan menyelesaikan transaksi QRIS dari meja.

PHP
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Jobs\SendWANotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentGatewayCallbackController extends Controller
{
    public function handleCallback(Request $request)
    {
        // Contoh penanganan struktur payload dari Midtrans
        $orderRef = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        
        $order = Order::where('order_reference', $orderRef)->first();
        
        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        if ($transactionStatus === 'settlement') {
            DB::transaction(function () use ($order) {
                // 1. Update status transaksi database
                $order->update([
                    'payment_status' => 'paid',
                    'order_status' => 'processing' // Masuk antrean dapur
                ]);

                // 2. Potong stok otomatis berdasarkan Bill of Materials (BOM)
                foreach ($order->items as $item) {
                    foreach ($item->product->recipes as $recipe) {
                        $recipe->ingredient->decrement('stock', $recipe->quantity * $item->quantity);
                    }
                }
            });

            // 3. Trigger Broadcasting Real-Time ke POS Tablet untuk Auto-Print / Fallback
            // event(new \\App\Events\OrderPaidRealtime($order->id));

            return response()->json(['message' => 'Callback diproses sukses']);
        }

        return response()->json(['message' => 'Status transaksi tidak memerlukan aksi']);
    }
}
D. Verifikasi Kehadiran Berbasis PIN (app/Http/Controllers/POS/TerminalController.php)
PHP
namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TerminalController extends Controller
{
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6',
            'action' => 'required|in:clock_in,clock_out'
        ]);

        // Cari user yang memiliki hak akses di sistem operasional POS
        $employees = User::whereIn('role', ['cashier', 'kitchen', 'admin'])->get();
        $matchedUser = null;

        foreach ($employees as $employee) {
            if (Hash::check($request->pin, $employee->pin)) {
                $matchedUser = $employee;
                break;
            }
        }

        if (!$matchedUser) {
            return response()->json(['status' => 'error', 'message' => 'PIN salah atau tidak terdaftar'], 401);
        }

        $today = now()->toDateString();

        if ($request->action === 'clock_in') {
            $exists = Attendance::where('user_id', $matchedUser->id)->where('date', $today)->exists();
            if ($exists) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah melakukan clock-in hari ini.'], 400);
            }

            Attendance::create([
                'user_id' => $matchedUser->id,
                'date' => $today,
                'clock_in' => now()->toTimeString(),
                'status' => now()->toTimeString() > '08:00:00' ? 'late' : 'ontime' // Contoh regulasi jam 8
            ]);

            return response()->json(['status' => 'success', 'message' => "Halo {$matchedUser->name}, Selamat bekerja!"]);
        }
        
        // Logika untuk Clock Out
        $attendance = Attendance::where('user_id', $matchedUser->id)->where('date', $today)->first();
        if (!$attendance || $attendance->clock_out !== null) {
            return response()->json(['status' => 'error', 'message' => 'Data kehadiran clock-in tidak ditemukan atau sudah melakukan clock-out.'], 400);
        }

        $attendance->update(['clock_out' => now()->toTimeString()]);
        return response()->json(['status' => 'success', 'message' => "Terima kasih dedikasinya hari ini, {$matchedUser->name}!"]);
    }
}
4. Struktur UI Komponen (Tailwind CSS Wireframing)
A. Layar Grid Pinpad Absensi POS (Ramah Sentuhan Tablet)
Tampilan modular dengan skema grid Tailwind CSS untuk input pin cepat.

HTML
<div class="flex flex-col items-center justify-center min-h-screen bg-slate-900 text-slate-100 p-4">
    <div class="w-full max-w-sm bg-slate-800 rounded-2xl shadow-xl border border-slate-700 p-6">
        <h3 class="text-xl font-bold text-center mb-2 tracking-wide">AUTHENTICATION REQUIRED</h3>
        <p class="text-xs text-slate-400 text-center mb-6">Masukkan 6-Digit PIN Anda untuk Absensi Mandiri</p>
        
        <div class="flex justify-center gap-3 mb-8">
            <span class="w-4 h-4 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></span>
            <span class="w-4 h-4 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></span>
            <span class="w-4 h-4 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></span>
            <span class="w-4 h-4 rounded-full bg-slate-600"></span>
            <span class="w-4 h-4 rounded-full bg-slate-600"></span>
            <span class="w-4 h-4 rounded-full bg-slate-600"></span>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-6">
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">1</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">2</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">3</button>
            
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">4</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">5</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">6</button>
            
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">7</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">8</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">9</button>
            
            <button class="h-16 text-sm font-bold bg-rose-600/30 text-rose-400 hover:bg-rose-600/40 rounded-xl transition duration-150">CLEAR</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">0</button>
            <button class="h-16 text-sm font-bold bg-emerald-600 text-white hover:bg-emerald-500 rounded-xl shadow-lg shadow-emerald-500/20 transition duration-150">ENTER</button>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button class="py-3 font-medium bg-slate-700 border-2 border-indigo-500 text-indigo-400 rounded-xl text-center text-sm">CLOCK IN</button>
            <button class="py-3 font-medium bg-slate-700 border-2 border-transparent text-slate-400 rounded-xl text-center text-sm hover:border-slate-500">CLOCK OUT</button>
        </div>
    </div>
</div>
B. Notifikasi Floating Fallback System di Layar Kasir
HTML
<div class="fixed bottom-5 right-5 z-50 max-w-sm bg-slate-800 border-l-4 border-emerald-500 rounded-xl shadow-2xl p-4 animate-bounce">
    <div class="flex items-start gap-3">
        <div class="p-2 bg-emerald-500/20 text-emerald-400 rounded-lg">
            <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-slate-200 text-sm">Pesanan Meja 5 Lunas via QRIS!</h4>
            <p class="text-xs text-slate-400 mt-1 mb-3">Invoice #INV-202605-0012. Harap double-check ke dapur apakah printer mencetak tiket otomatis.</p>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 text-xs font-semibold bg-emerald-600 text-white rounded-md hover:bg-emerald-500 transition duration-150">
                    Selesai / Aman
                </button>
                <button class="px-3 py-1.5 text-xs font-semibold bg-slate-700 text-slate-300 rounded-md hover:bg-slate-600 transition duration-150">
                    Cetak Tiket Manual
                </button>
            </div>
        </div>
    </div>
</div>
Dokumen ini dipersiapkan sebagai panduan cetak biru awal siklus hidup perangkat lunak. Seluruh struktur relasi tabel basis data telah dioptimalkan untuk meminimalkan data redundan (Normalisasi Form Ketiga/3NF).
"""
^
SyntaxError: (unicode error) 'unicodeescape' codec can't decode bytes in position 12449-12450: truncated \UXXXXXXXX escape

PHP
// database/migrations/2026_01_01_000001_create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('pin', 64)->unique(); // Hashed PIN untuk absensi POS
    $table->enum('role', ['owner', 'admin', 'cashier', 'kitchen']);
    $table->rememberToken();
    $table->timestamps();
});

// database/migrations/2026_01_01_000002_create_shifts_table.php
Schema::create('shifts', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // e.g., Morning, Night
    $table->time('start_time');
    $table->time('end_time');
    $table->timestamps();
});

// database/migrations/2026_01_01_000003_create_schedules_table.php
Schema::create('schedules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('shift_id')->constrained()->onDelete('cascade');
    $table->date('date');
    $table->timestamps();
});

// database/migrations/2026_01_01_000004_create_attendances_table.php
Schema::create('attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->date('date');
    $table->time('clock_in');
    $table->time('clock_out')->nullable();
    $table->enum('status', ['ontime', 'late', 'absent']);
    $table->timestamps();
});
B. Modul Menu, Inventaris, & Bill of Materials (BOM)
PHP
// database/migrations/2026_01_01_000005_create_products_table.php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('category'); // e.g., Food, Beverage, Coffee
    $table->decimal('price', 10, 2);
    $table->string('image')->nullable();
    $table->boolean('is_available')->default(true);
    $table->timestamps();
});

// database/migrations/2026_01_01_000006_create_ingredients_table.php
Schema::create('ingredients', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->integer('stock')->default(0);
    $table->string('unit'); // e.g., gram, ml, pcs
    $table->integer('minimum_stock')->default(10);
    $table->timestamps();
});

// database/migrations/2026_01_01_000007_create_recipes_table.php (Bill of Materials)
Schema::create('recipes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
    $table->integer('quantity'); // Jumlah bahan baku yang digunakan per porsi
    $table->timestamps();
});
C. Modul Meja, Transaksi POS, & Self-Ordering QR
PHP
// database/migrations/2026_01_01_000008_create_tables_table.php
Schema::create('tables', function (Blueprint $table) {
    $table->id();
    $table->string('table_number', 10);
    $table->string('qr_token')->unique(); // Token pengaman untuk URL Scan QR
    $table->enum('status', ['empty', 'occupied'])->default('empty');
    $table->timestamps();
});

// database/migrations/2026_01_01_000009_create_orders_table.php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_reference', 50)->unique(); // Invoice ID
    $table->foreignId('table_id')->nullable()->constrained();
    $table->enum('order_type', ['dine_in', 'takeaway']);
    $table->decimal('total_amount', 10, 2);
    $table->enum('payment_method', ['cash', 'qris_gateway'])->nullable();
    $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid');
    $table->enum('order_status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
    $table->string('gateway_transaction_id')->nullable(); // Untuk Midtrans/Xendit callback
    $table->timestamps();
});

// database/migrations/2026_01_01_000010_create_order_items_table.php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained();
    $table->integer('quantity');
    $table->decimal('price', 10, 2); // Harga saat dibeli
    $table->timestamps();
});
D. Modul Rekrutmen (ATS)
PHP
// database/migrations/2026_01_01_000011_create_job_vacancies_table.php
Schema::create('job_vacancies', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->text('requirements');
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->timestamps();
});

// database/migrations/2026_01_01_000012_create_job_applications_table.php
Schema::create('job_applications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_vacancy_id')->constrained()->onDelete('cascade');
    $table->string('applicant_name');
    $table->string('email');
    $table->string('phone_number');
    $table->string('cv_path'); // Path file privat di secured storage
    $table->enum('status', ['submitted', 'screening', 'interview', 'accepted', 'rejected'])->default('submitted');
    $table->timestamps();
});
2. Struktur Routing Sistem (routes/)
Pembagian rute dipisahkan secara modular untuk mengamankan hak akses data internal dan eksternal.

A. Rute Web (routes/web.php)
PHP
use App\Http\Controllers\Front\ComproController;
use App\Http\Controllers\Front\SelfOrderController;
use App\Http\Controllers\POS\TerminalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HRController;

// --- 1. FRONT-END / COMPANY PROFILE (Akses Publik) ---
Route::get('/', [ComproController::class, 'index'])->name('compro.home');
Route::get('/menu', [ComproController::class, 'menu'])->name('compro.menu');
Route::get('/careers', [ComproController::class, 'careers'])->name('compro.careers');
Route::post('/careers/apply', [ComproController::class, 'apply'])->name('compro.apply');

// --- 2. DINE-IN SELF ORDERING (Akses via QR Code Meja) ---
Route::group(['prefix' => 'table-order', 'middleware' => 'validate.table.token'], function () {
    Route::get('/', [SelfOrderController::class, 'index'])->name('self_order.index');
    Route::post('/checkout', [SelfOrderController::class, 'checkout'])->name('self_order.checkout');
});

// --- 3. TERMINAL POS & ABSENSI (Akses Tablet / Staf Restoran) ---
Route::group(['prefix' => 'pos', 'middleware' => 'auth.pos'], function () {
    Route::get('/', [TerminalController::class, 'index'])->name('pos.terminal');
    Route::post('/attendance/verify', [TerminalController::class, 'verifyPin'])->name('pos.attendance.pin');
    Route::post('/order/store', [TerminalController::class, 'storeOrder'])->name('pos.order.store');
});

// --- 4. BACK OFFICE / ADMIN PANEL (Akses Owner & Admin/HR) ---
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:owner,admin']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Manajemen HR & Penjadwalan
    Route::get('/hr/schedules', [HRController::class, 'schedules'])->name('admin.hr.schedules');
    Route::get('/hr/attendances', [HRController::class, 'attendances'])->name('admin.hr.attendances');
    Route::get('/hr/applicants', [HRController::class, 'applicants'])->name('admin.hr.applicants');
    Route::patch('/hr/applicants/{id}/status', [HRController::class, 'updateApplicantStatus'])->name('admin.hr.applicants.update');
    
    // Inventaris & Gudang
    Route::resource('ingredients', \App\Http\Controllers\Admin\IngredientController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
});
B. Rute API (routes/api.php)
PHP
use App\Http\Controllers\API\PaymentGatewayCallbackController;

// Webhook Handler untuk Payment Gateway (Midtrans / Xendit)
Route::post('/payment/callback', [PaymentGatewayCallbackController::class, 'handleCallback']);
3. Komponen Logika Inti & Integrasi Pihak Ketiga
A. Fonnte WhatsApp Service (app/Services/FonnteService.php)
Fungsi mandiri memanfaatkan HTTP Client Laravel untuk mengirimkan notifikasi asinkronus ke WhatsApp.

PHP
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService 
{
    /**
     * Kirim Pesan Teks via Fonnte API
     */
    public static function sendMessage(string $target, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])->timeout(10)->post('[https://api.fonnte.com/send](https://api.fonnte.com/send)', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62'
            ]);

            return $response->json();
        } catch (\\Exception $e) {
            Log::error("Fonnte API Error: " . $e->getMessage());
            return ['status' => false, 'reason' => $e->getMessage()];
        }
    }
}
B. Background Queue Job (app/Jobs/SendWANotification.php)
PHP
namespace App\Jobs;

use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWANotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $phoneNumber, 
        protected string $message
    ) {}

    public function handle(): void
    {
        FonnteService::sendMessage($this->phoneNumber, $this->message);
    }
}
C. Webhook Payment Handler (app/Http/Controllers/API/PaymentGatewayCallbackController.php)
Menangani pembaruan status pemesanan instan saat pelanggan menyelesaikan transaksi QRIS dari meja.

PHP
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Jobs\SendWANotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentGatewayCallbackController extends Controller
{
    public function handleCallback(Request $request)
    {
        // Contoh penanganan struktur payload dari Midtrans
        $orderRef = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        
        $order = Order::where('order_reference', $orderRef)->first();
        
        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        if ($transactionStatus === 'settlement') {
            DB::transaction(function () use ($order) {
                // 1. Update status transaksi database
                $order->update([
                    'payment_status' => 'paid',
                    'order_status' => 'processing' // Masuk antrean dapur
                ]);

                // 2. Potong stok otomatis berdasarkan Bill of Materials (BOM)
                foreach ($order->items as $item) {
                    foreach ($item->product->recipes as $recipe) {
                        $recipe->ingredient->decrement('stock', $recipe->quantity * $item->quantity);
                    }
                }
            });

            // 3. Trigger Broadcasting Real-Time ke POS Tablet untuk Auto-Print / Fallback
            // event(new \\App\Events\OrderPaidRealtime($order->id));

            return response()->json(['message' => 'Callback diproses sukses']);
        }

        return response()->json(['message' => 'Status transaksi tidak memerlukan aksi']);
    }
}
D. Verifikasi Kehadiran Berbasis PIN (app/Http/Controllers/POS/TerminalController.php)
PHP
namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TerminalController extends Controller
{
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6',
            'action' => 'required|in:clock_in,clock_out'
        ]);

        // Cari user yang memiliki hak akses di sistem operasional POS
        $employees = User::whereIn('role', ['cashier', 'kitchen', 'admin'])->get();
        $matchedUser = null;

        foreach ($employees as $employee) {
            if (Hash::check($request->pin, $employee->pin)) {
                $matchedUser = $employee;
                break;
            }
        }

        if (!$matchedUser) {
            return response()->json(['status' => 'error', 'message' => 'PIN salah atau tidak terdaftar'], 401);
        }

        $today = now()->toDateString();

        if ($request->action === 'clock_in') {
            $exists = Attendance::where('user_id', $matchedUser->id)->where('date', $today)->exists();
            if ($exists) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah melakukan clock-in hari ini.'], 400);
            }

            Attendance::create([
                'user_id' => $matchedUser->id,
                'date' => $today,
                'clock_in' => now()->toTimeString(),
                'status' => now()->toTimeString() > '08:00:00' ? 'late' : 'ontime' // Contoh regulasi jam 8
            ]);

            return response()->json(['status' => 'success', 'message' => "Halo {$matchedUser->name}, Selamat bekerja!"]);
        }
        
        // Logika untuk Clock Out
        $attendance = Attendance::where('user_id', $matchedUser->id)->where('date', $today)->first();
        if (!$attendance || $attendance->clock_out !== null) {
            return response()->json(['status' => 'error', 'message' => 'Data kehadiran clock-in tidak ditemukan atau sudah melakukan clock-out.'], 400);
        }

        $attendance->update(['clock_out' => now()->toTimeString()]);
        return response()->json(['status' => 'success', 'message' => "Terima kasih dedikasinya hari ini, {$matchedUser->name}!"]);
    }
}
4. Struktur UI Komponen (Tailwind CSS Wireframing)
A. Layar Grid Pinpad Absensi POS (Ramah Sentuhan Tablet)
Tampilan modular dengan skema grid Tailwind CSS untuk input pin cepat.

HTML
<div class="flex flex-col items-center justify-center min-h-screen bg-slate-900 text-slate-100 p-4">
    <div class="w-full max-w-sm bg-slate-800 rounded-2xl shadow-xl border border-slate-700 p-6">
        <h3 class="text-xl font-bold text-center mb-2 tracking-wide">AUTHENTICATION REQUIRED</h3>
        <p class="text-xs text-slate-400 text-center mb-6">Masukkan 6-Digit PIN Anda untuk Absensi Mandiri</p>
        
        <div class="flex justify-center gap-3 mb-8">
            <span class="w-4 h-4 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></span>
            <span class="w-4 h-4 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></span>
            <span class="w-4 h-4 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></span>
            <span class="w-4 h-4 rounded-full bg-slate-600"></span>
            <span class="w-4 h-4 rounded-full bg-slate-600"></span>
            <span class="w-4 h-4 rounded-full bg-slate-600"></span>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-6">
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">1</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">2</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">3</button>
            
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">4</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">5</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">6</button>
            
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">7</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">8</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">9</button>
            
            <button class="h-16 text-sm font-bold bg-rose-600/30 text-rose-400 hover:bg-rose-600/40 rounded-xl transition duration-150">CLEAR</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">0</button>
            <button class="h-16 text-sm font-bold bg-emerald-600 text-white hover:bg-emerald-500 rounded-xl shadow-lg shadow-emerald-500/20 transition duration-150">ENTER</button>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button class="py-3 font-medium bg-slate-700 border-2 border-indigo-500 text-indigo-400 rounded-xl text-center text-sm">CLOCK IN</button>
            <button class="py-3 font-medium bg-slate-700 border-2 border-transparent text-slate-400 rounded-xl text-center text-sm hover:border-slate-500">CLOCK OUT</button>
        </div>
    </div>
</div>
B. Notifikasi Floating Fallback System di Layar Kasir
HTML
<div class="fixed bottom-5 right-5 z-50 max-w-sm bg-slate-800 border-l-4 border-emerald-500 rounded-xl shadow-2xl p-4 animate-bounce">
    <div class="flex items-start gap-3">
        <div class="p-2 bg-emerald-500/20 text-emerald-400 rounded-lg">
            <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-slate-200 text-sm">Pesanan Meja 5 Lunas via QRIS!</h4>
            <p class="text-xs text-slate-400 mt-1 mb-3">Invoice #INV-202605-0012. Harap double-check ke dapur apakah printer mencetak tiket otomatis.</p>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 text-xs font-semibold bg-emerald-600 text-white rounded-md hover:bg-emerald-500 transition duration-150">
                    Selesai / Aman
                </button>
                <button class="px-3 py-1.5 text-xs font-semibold bg-slate-700 text-slate-300 rounded-md hover:bg-slate-600 transition duration-150">
                    Cetak Tiket Manual
                </button>
            </div>
        </div>
    </div>
</div>
Dokumen ini dipersiapkan sebagai panduan cetak biru awal siklus hidup perangkat lunak. Seluruh struktur relasi tabel basis data telah dioptimalkan untuk meminimalkan data redundan (Normalisasi Form Ketiga/3NF).
"""

with open("blueprint-manajemen-fnb.md", "w", encoding="utf-8") as file:
file.write(markdown_content)
print("File generated successfully.")

```python?code_reference&code_event_index=6
markdown_content = r"""# Blueprint Sistem Manajemen F&B (Single Outlet)
## Laravel 10, Tailwind CSS, & MySQL

Dokumen ini berisi rancangan struktur basis data (migrasi), konfigurasi routing, komponen logika (Service & Controller), serta arsitektur antarmuka menggunakan Tailwind CSS untuk sistem manajemen F&B terintegrasi.

---

## 1. Arsitektur Basis Data (Laravel Migrations & MySQL)

Berikut adalah rancangan tabel MySQL yang dikonfigurasi melalui skema migrasi Laravel 10 untuk mendukung relasi antar-modul.

### A. Modul HR, Shift, dan Absensi PIN

B. Modul Menu, Inventaris, & Bill of Materials (BOM)
PHP
// database/migrations/2026_01_01_000005_create_products_table.php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('category'); // e.g., Food, Beverage, Coffee
    $table->decimal('price', 10, 2);
    $table->string('image')->nullable();
    $table->boolean('is_available')->default(true);
    $table->timestamps();
});

// database/migrations/2026_01_01_000006_create_ingredients_table.php
Schema::create('ingredients', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->integer('stock')->default(0);
    $table->string('unit'); // e.g., gram, ml, pcs
    $table->integer('minimum_stock')->default(10);
    $table->timestamps();
});

// database/migrations/2026_01_01_000007_create_recipes_table.php (Bill of Materials)
Schema::create('recipes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
    $table->integer('quantity'); // Jumlah bahan baku yang digunakan per porsi
    $table->timestamps();
});
C. Modul Meja, Transaksi POS, & Self-Ordering QR
PHP
// database/migrations/2026_01_01_000008_create_tables_table.php
Schema::create('tables', function (Blueprint $table) {
    $table->id();
    $table->string('table_number', 10);
    $table->string('qr_token')->unique(); // Token pengaman untuk URL Scan QR
    $table->enum('status', ['empty', 'occupied'])->default('empty');
    $table->timestamps();
});

// database/migrations/2026_01_01_000009_create_orders_table.php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_reference', 50)->unique(); // Invoice ID
    $table->foreignId('table_id')->nullable()->constrained();
    $table->enum('order_type', ['dine_in', 'takeaway']);
    $table->decimal('total_amount', 10, 2);
    $table->enum('payment_method', ['cash', 'qris_gateway'])->nullable();
    $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid');
    $table->enum('order_status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
    $table->string('gateway_transaction_id')->nullable(); // Untuk Midtrans/Xendit callback
    $table->timestamps();
});

// database/migrations/2026_01_01_000010_create_order_items_table.php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained();
    $table->integer('quantity');
    $table->decimal('price', 10, 2); // Harga saat dibeli
    $table->timestamps();
});
D. Modul Rekrutmen (ATS)
PHP
// database/migrations/2026_01_01_000011_create_job_vacancies_table.php
Schema::create('job_vacancies', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->text('requirements');
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->timestamps();
});

// database/migrations/2026_01_01_000012_create_job_applications_table.php
Schema::create('job_applications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_vacancy_id')->constrained()->onDelete('cascade');
    $table->string('applicant_name');
    $table->string('email');
    $table->string('phone_number');
    $table->string('cv_path'); // Path file privat di secured storage
    $table->enum('status', ['submitted', 'screening', 'interview', 'accepted', 'rejected'])->default('submitted');
    $table->timestamps();
});
2. Struktur Routing Sistem (routes/)
Pembagian rute dipisahkan secara modular untuk mengamankan hak akses data internal dan eksternal.

A. Rute Web (routes/web.php)
PHP
use App\Http\Controllers\Front\ComproController;
use App\Http\Controllers\Front\SelfOrderController;
use App\Http\Controllers\POS\TerminalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HRController;

// --- 1. FRONT-END / COMPANY PROFILE (Akses Publik) ---
Route::get('/', [ComproController::class, 'index'])->name('compro.home');
Route::get('/menu', [ComproController::class, 'menu'])->name('compro.menu');
Route::get('/careers', [ComproController::class, 'careers'])->name('compro.careers');
Route::post('/careers/apply', [ComproController::class, 'apply'])->name('compro.apply');

// --- 2. DINE-IN SELF ORDERING (Akses via QR Code Meja) ---
Route::group(['prefix' => 'table-order', 'middleware' => 'validate.table.token'], function () {
    Route::get('/', [SelfOrderController::class, 'index'])->name('self_order.index');
    Route::post('/checkout', [SelfOrderController::class, 'checkout'])->name('self_order.checkout');
});

// --- 3. TERMINAL POS & ABSENSI (Akses Tablet / Staf Restoran) ---
Route::group(['prefix' => 'pos', 'middleware' => 'auth.pos'], function () {
    Route::get('/', [TerminalController::class, 'index'])->name('pos.terminal');
    Route::post('/attendance/verify', [TerminalController::class, 'verifyPin'])->name('pos.attendance.pin');
    Route::post('/order/store', [TerminalController::class, 'storeOrder'])->name('pos.order.store');
});

// --- 4. BACK OFFICE / ADMIN PANEL (Akses Owner & Admin/HR) ---
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:owner,admin']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Manajemen HR & Penjadwalan
    Route::get('/hr/schedules', [HRController::class, 'schedules'])->name('admin.hr.schedules');
    Route::get('/hr/attendances', [HRController::class, 'attendances'])->name('admin.hr.attendances');
    Route::get('/hr/applicants', [HRController::class, 'applicants'])->name('admin.hr.applicants');
    Route::patch('/hr/applicants/{id}/status', [HRController::class, 'updateApplicantStatus'])->name('admin.hr.applicants.update');
    
    // Inventaris & Gudang
    Route::resource('ingredients', \App\Http\Controllers\Admin\IngredientController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
});
B. Rute API (routes/api.php)
PHP
use App\Http\Controllers\API\PaymentGatewayCallbackController;

// Webhook Handler untuk Payment Gateway (Midtrans / Xendit)
Route::post('/payment/callback', [PaymentGatewayCallbackController::class, 'handleCallback']);
3. Komponen Logika Inti & Integrasi Pihak Ketiga
A. Fonnte WhatsApp Service (app/Services/FonnteService.php)
Fungsi mandiri memanfaatkan HTTP Client Laravel untuk mengirimkan notifikasi asinkronus ke WhatsApp.

PHP
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService 
{
    /**
     * Kirim Pesan Teks via Fonnte API
     */
    public static function sendMessage(string $target, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])->timeout(10)->post('[https://api.fonnte.com/send](https://api.fonnte.com/send)', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62'
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Fonnte API Error: " . $e->getMessage());
            return ['status' => false, 'reason' => $e->getMessage()];
        }
    }
}
B. Background Queue Job (app/Jobs/SendWANotification.php)
PHP
namespace App\Jobs;

use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWANotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $phoneNumber, 
        protected string $message
    ) {}

    public function handle(): void
    {
        FonnteService::sendMessage($this->phoneNumber, $this->message);
    }
}
C. Webhook Payment Handler (app/Http/Controllers/API/PaymentGatewayCallbackController.php)
Menangani pembaruan status pemesanan instan saat pelanggan menyelesaikan transaksi QRIS dari meja.

PHP
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Jobs\SendWANotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentGatewayCallbackController extends Controller
{
    public function handleCallback(Request $request)
    {
        // Contoh penanganan struktur payload dari Midtrans
        $orderRef = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        
        $order = Order::where('order_reference', $orderRef)->first();
        
        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        if ($transactionStatus === 'settlement') {
            DB::transaction(function () use ($order) {
                // 1. Update status transaksi database
                $order->update([
                    'payment_status' => 'paid',
                    'order_status' => 'processing' // Masuk antrean dapur
                ]);

                // 2. Potong stok otomatis berdasarkan Bill of Materials (BOM)
                foreach ($order->items as $item) {
                    foreach ($item->product->recipes as $recipe) {
                        $recipe->ingredient->decrement('stock', $recipe->quantity * $item->quantity);
                    }
                }
            });

            // 3. Trigger Broadcasting Real-Time ke POS Tablet untuk Auto-Print / Fallback
            // event(new \App\Events\OrderPaidRealtime($order->id));

            return response()->json(['message' => 'Callback diproses sukses']);
        }

        return response()->json(['message' => 'Status transaksi tidak memerlukan aksi']);
    }
}
D. Verifikasi Kehadiran Berbasis PIN (app/Http/Controllers/POS/TerminalController.php)
PHP
namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TerminalController extends Controller
{
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6',
            'action' => 'required|in:clock_in,clock_out'
        ]);

        // Cari user yang memiliki hak akses di sistem operasional POS
        $employees = User::whereIn('role', ['cashier', 'kitchen', 'admin'])->get();
        $matchedUser = null;

        foreach ($employees as $employee) {
            if (Hash::check($request->pin, $employee->pin)) {
                $matchedUser = $employee;
                break;
            }
        }

        if (!$matchedUser) {
            return response()->json(['status' => 'error', 'message' => 'PIN salah atau tidak terdaftar'], 401);
        }

        $today = now()->toDateString();

        if ($request->action === 'clock_in') {
            $exists = Attendance::where('user_id', $matchedUser->id)->where('date', $today)->exists();
            if ($exists) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah melakukan clock-in hari ini.'], 400);
            }

            Attendance::create([
                'user_id' => $matchedUser->id,
                'date' => $today,
                'clock_in' => now()->toTimeString(),
                'status' => now()->toTimeString() > '08:00:00' ? 'late' : 'ontime' // Contoh regulasi jam 8
            ]);

            return response()->json(['status' => 'success', 'message' => "Halo {$matchedUser->name}, Selamat bekerja!"]);
        }
        
        // Logika untuk Clock Out
        $attendance = Attendance::where('user_id', $matchedUser->id)->where('date', $today)->first();
        if (!$attendance || $attendance->clock_out !== null) {
            return response()->json(['status' => 'error', 'message' => 'Data kehadiran clock-in tidak ditemukan atau sudah melakukan clock-out.'], 400);
        }

        $attendance->update(['clock_out' => now()->toTimeString()]);
        return response()->json(['status' => 'success', 'message' => "Terima kasih dedikasinya hari ini, {$matchedUser->name}!"]);
    }
}
4. Struktur UI Komponen (Tailwind CSS Wireframing)
A. Layar Grid Pinpad Absensi POS (Ramah Sentuhan Tablet)
Tampilan modular dengan skema grid Tailwind CSS untuk input pin cepat.

HTML
<div class="flex flex-col items-center justify-center min-h-screen bg-slate-900 text-slate-100 p-4">
    <div class="w-full max-w-sm bg-slate-800 rounded-2xl shadow-xl border border-slate-700 p-6">
        <h3 class="text-xl font-bold text-center mb-2 tracking-wide">AUTHENTICATION REQUIRED</h3>
        <p class="text-xs text-slate-400 text-center mb-6">Masukkan 6-Digit PIN Anda untuk Absensi Mandiri</p>
        
        <div class="flex justify-center gap-3 mb-8">
            <span class="w-4 h-4 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></span>
            <span class="w-4 h-4 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></span>
            <span class="w-4 h-4 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></span>
            <span class="w-4 h-4 rounded-full bg-slate-600"></span>
            <span class="w-4 h-4 rounded-full bg-slate-600"></span>
            <span class="w-4 h-4 rounded-full bg-slate-600"></span>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-6">
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">1</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">2</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">3</button>
            
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">4</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">5</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">6</button>
            
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">7</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">8</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">9</button>
            
            <button class="h-16 text-sm font-bold bg-rose-600/30 text-rose-400 hover:bg-rose-600/40 rounded-xl transition duration-150">CLEAR</button>
            <button class="h-16 text-xl font-semibold bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-xl transition duration-150">0</button>
            <button class="h-16 text-sm font-bold bg-emerald-600 text-white hover:bg-emerald-500 rounded-xl shadow-lg shadow-emerald-500/20 transition duration-150">ENTER</button>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button class="py-3 font-medium bg-slate-700 border-2 border-indigo-500 text-indigo-400 rounded-xl text-center text-sm">CLOCK IN</button>
            <button class="py-3 font-medium bg-slate-700 border-2 border-transparent text-slate-400 rounded-xl text-center text-sm hover:border-slate-500">CLOCK OUT</button>
        </div>
    </div>
</div>
B. Notifikasi Floating Fallback System di Layar Kasir
HTML
<div class="fixed bottom-5 right-5 z-50 max-w-sm bg-slate-800 border-l-4 border-emerald-500 rounded-xl shadow-2xl p-4 animate-bounce">
    <div class="flex items-start gap-3">
        <div class="p-2 bg-emerald-500/20 text-emerald-400 rounded-lg">
            <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-slate-200 text-sm">Pesanan Meja 5 Lunas via QRIS!</h4>
            <p class="text-xs text-slate-400 mt-1 mb-3">Invoice #INV-202605-0012. Harap double-check ke dapur apakah printer mencetak tiket otomatis.</p>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 text-xs font-semibold bg-emerald-600 text-white rounded-md hover:bg-emerald-500 transition duration-150">
                    Selesai / Aman
                </button>
                <button class="px-3 py-1.5 text-xs font-semibold bg-slate-700 text-slate-300 rounded-md hover:bg-slate-600 transition duration-150">
                    Cetak Tiket Manual
                </button>
            </div>
        </div>
    </div>
</div>
Dokumen ini dipersiapkan sebagai panduan cetak biru awal siklus hidup perangkat lunak. Seluruh struktur relasi tabel basis data telah dioptimalkan untuk meminimalkan data redundan (Normalisasi Form Ketiga/3NF).