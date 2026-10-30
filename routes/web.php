<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AtsController;

// Admin Controllers for Inventory
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\GoodsReceiptController;
use App\Http\Controllers\Admin\StockOpnameController;
use App\Http\Controllers\Admin\StockTransferController;
use App\Http\Controllers\Admin\StockMutationController;
use App\Http\Controllers\Admin\WasteController;
use App\Http\Controllers\Admin\RecipeController;

// Public routes
Route::get('/', function () {
    $categories = \App\Models\Category::with(['products' => function($q) {
        $q->where('is_available', true);
    }])->get();
    
    return view('welcome', compact('categories'));
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
<<<<<<< HEAD
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.post');
=======
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ATS (Careers) - Publicly accessible
Route::prefix('careers')->group(function () {
    Route::get('/', [AtsController::class, 'jobs'])->name('careers.jobs');
<<<<<<< HEAD
    Route::post('/apply', [AtsController::class, 'apply'])->middleware('throttle:careers')->name('careers.apply');
=======
    Route::post('/apply', [AtsController::class, 'apply'])->name('careers.apply');
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    
    // POS Routes
    Route::prefix('pos')->middleware(\App\Http\Middleware\AdminMiddleware::class.':cashier,owner')->group(function () {
        Route::get('/shift/open', [\App\Http\Controllers\PosShiftController::class, 'open'])->name('pos.shift.open');
        Route::post('/shift/open', [\App\Http\Controllers\PosShiftController::class, 'storeOpen'])->name('pos.shift.storeOpen');
        Route::get('/shift/close', [\App\Http\Controllers\PosShiftController::class, 'close'])->name('pos.shift.close');
        Route::post('/shift/close', [\App\Http\Controllers\PosShiftController::class, 'storeClose'])->name('pos.shift.storeClose');
        Route::get('/shift/summary/{id}', [\App\Http\Controllers\PosShiftController::class, 'summary'])->name('pos.shift.summary');

        Route::get('/menu', [PosController::class, 'menu'])->name('pos.menu');
<<<<<<< HEAD
        Route::post('/order', [PosController::class, 'storeOrder'])->middleware('throttle:orders')->name('pos.order.store');
=======
        Route::post('/order', [PosController::class, 'storeOrder'])->name('pos.order.store');
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
        Route::get('/receipt/{order}', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::post('/table-payment/{order}', [PosController::class, 'processTablePayment'])->name('pos.table-payment');
        Route::get('/live-status', [PosController::class, 'liveStatus'])->name('pos.live-status');
    });

    // Waiter Dashboard
    Route::prefix('waiter')->middleware(\App\Http\Middleware\AdminMiddleware::class.':waiter')->group(function () {
        Route::get('/', [\App\Http\Controllers\WaiterController::class, 'index'])->name('waiter.index');
        Route::post('/{order}/serve', [\App\Http\Controllers\WaiterController::class, 'markAsServed'])->name('waiter.serve');
    });

    // Kitchen Routes
    Route::prefix('kitchen')->middleware(\App\Http\Middleware\AdminMiddleware::class.':kitchen')->group(function () {
        Route::get('/', [\App\Http\Controllers\KitchenController::class, 'index'])->name('kitchen.index');
        Route::post('/{order}/ready', [\App\Http\Controllers\KitchenController::class, 'markAsReady'])->name('kitchen.ready');
    });

    // HR Routes (for employees)
    Route::prefix('hr')->group(function () {
        Route::get('/', [HrController::class, 'dashboard'])->name('hr.dashboard');
<<<<<<< HEAD
        Route::post('/clock-in', [HrController::class, 'clockIn'])->middleware('throttle:hr-attendance')->name('hr.clockIn');
        Route::post('/clock-out', [HrController::class, 'clockOut'])->middleware('throttle:hr-attendance')->name('hr.clockOut');
        Route::post('/register-face', [HrController::class, 'registerFace'])->middleware('throttle:hr-attendance')->name('hr.registerFace');
=======
        Route::post('/clock-in', [HrController::class, 'clockIn'])->name('hr.clockIn');
        Route::post('/clock-out', [HrController::class, 'clockOut'])->name('hr.clockOut');
        Route::post('/register-face', [HrController::class, 'registerFace'])->name('hr.registerFace');
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
    });

    // Inventory Public (if any)
    Route::prefix('inventory')->middleware(\App\Http\Middleware\AdminMiddleware::class.':inventory,kitchen')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
<<<<<<< HEAD
=======
        Route::post('/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update');
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
    });

    // Admin Routes
    Route::prefix('admin')->group(function () {
        
        // Admin & Owner Shared
        Route::middleware(\App\Http\Middleware\AdminMiddleware::class.':owner')->group(function() {
            Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
            Route::get('pos/sales-report', [\App\Http\Controllers\Admin\SalesReportController::class, 'index'])->name('admin.pos.sales_report');
            Route::get('owner/report', [\App\Http\Controllers\Admin\OwnerReportController::class, 'index'])->name('admin.owner.report');
        });

        // Admin Only Features
        Route::middleware(\App\Http\Middleware\AdminMiddleware::class.':owner')->group(function() {

            // Orders Management
            Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
            Route::post('/orders/{order}/cancel', [\App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('admin.orders.cancel');

            // Categories, Products, Variants, Packages, Areas, Tables, Reservations
            Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories.index');
            Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
            Route::post('/categories/{category}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
            Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('admin.categories.destroy');

            Route::get('/products', [AdminController::class, 'products'])->name('admin.products.index');
            Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
            Route::post('/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
            Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

            Route::get('/variants', [AdminController::class, 'variants'])->name('admin.variants.index');
            Route::post('/variants', [AdminController::class, 'storeVariant'])->name('admin.variants.store');
            Route::post('/variants/{variant}', [AdminController::class, 'updateVariant'])->name('admin.variants.update');
            Route::delete('/variants/{variant}', [AdminController::class, 'destroyVariant'])->name('admin.variants.destroy');
            Route::post('/variant-options', [AdminController::class, 'storeVariantOption'])->name('admin.variant_options.store');
            Route::delete('/variant-options/{option}', [AdminController::class, 'destroyVariantOption'])->name('admin.variant_options.destroy');

            Route::get('/packages', [AdminController::class, 'packages'])->name('admin.packages.index');
            Route::post('/packages', [AdminController::class, 'storePackage'])->name('admin.packages.store');
            Route::post('/package-items', [AdminController::class, 'storePackageItem'])->name('admin.package_items.store');
            Route::delete('/package-items/{item}', [AdminController::class, 'destroyPackageItem'])->name('admin.package_items.destroy');

            Route::get('/areas', [AdminController::class, 'areas'])->name('admin.areas.index');
            Route::post('/areas', [AdminController::class, 'storeArea'])->name('admin.areas.store');
            Route::post('/areas/{area}', [AdminController::class, 'updateArea'])->name('admin.areas.update');
            Route::delete('/areas/{area}', [AdminController::class, 'destroyArea'])->name('admin.areas.destroy');

            Route::get('/tables', [AdminController::class, 'tables'])->name('admin.tables.index');
            Route::post('/tables', [AdminController::class, 'storeTable'])->name('admin.tables.store');
            Route::post('/tables/{table}', [AdminController::class, 'updateTable'])->name('admin.tables.update');
            Route::delete('/tables/{table}', [AdminController::class, 'destroyTable'])->name('admin.tables.destroy');

            Route::get('/reservations', [AdminController::class, 'reservations'])->name('admin.reservations.index');
            Route::post('/reservations', [AdminController::class, 'storeReservation'])->name('admin.reservations.store');
            Route::post('/reservations/{reservation}', [AdminController::class, 'updateReservationStatus'])->name('admin.reservations.update');
            Route::delete('/reservations/{reservation}', [AdminController::class, 'destroyReservation'])->name('admin.reservations.destroy');

            Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)->names('admin.customers');
            Route::get('settings/loyalty', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.loyalty');
            Route::post('settings/loyalty', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.loyalty.update');
            Route::get('settings/birthday', [\App\Http\Controllers\Admin\SettingController::class, 'birthday'])->name('admin.settings.birthday');
            Route::post('settings/birthday', [\App\Http\Controllers\Admin\SettingController::class, 'updateBirthday'])->name('admin.settings.birthday.update');
            
            Route::get('settings/attendance', [\App\Http\Controllers\Admin\SettingController::class, 'attendance'])->name('admin.settings.attendance');
            Route::post('settings/attendance', [\App\Http\Controllers\Admin\SettingController::class, 'updateAttendance'])->name('admin.settings.attendance.update');
            
            

            // ===== Pengaturan Kafe =====
            Route::prefix('cafe')->name('admin.cafe.')->group(function () {
                // Profil
                Route::get('/profile',  [\App\Http\Controllers\Admin\CafeSettingController::class, 'profile'])->name('profile');
                Route::post('/profile', [\App\Http\Controllers\Admin\CafeSettingController::class, 'updateProfile'])->name('profile.update');
                // Master Satuan
                Route::get('/units',          [\App\Http\Controllers\Admin\CafeSettingController::class, 'units'])->name('units');
                Route::post('/units',         [\App\Http\Controllers\Admin\CafeSettingController::class, 'storeUnit'])->name('units.store');
                Route::put('/units/{unit}',   [\App\Http\Controllers\Admin\CafeSettingController::class, 'updateUnit'])->name('units.update');
                Route::delete('/units/{unit}',[\App\Http\Controllers\Admin\CafeSettingController::class, 'destroyUnit'])->name('units.destroy');
                // Metode Pembayaran
                Route::get('/payment-methods',             [\App\Http\Controllers\Admin\CafeSettingController::class, 'paymentMethods'])->name('payment_methods');
                Route::post('/payment-methods',            [\App\Http\Controllers\Admin\CafeSettingController::class, 'storePaymentMethod'])->name('payment_methods.store');
                Route::put('/payment-methods/{method}',    [\App\Http\Controllers\Admin\CafeSettingController::class, 'updatePaymentMethod'])->name('payment_methods.update');
                Route::delete('/payment-methods/{method}', [\App\Http\Controllers\Admin\CafeSettingController::class, 'destroyPaymentMethod'])->name('payment_methods.destroy');
                // Konfigurasi Pajak
                Route::get('/tax',  [\App\Http\Controllers\Admin\CafeSettingController::class, 'tax'])->name('tax');
                Route::post('/tax', [\App\Http\Controllers\Admin\CafeSettingController::class, 'updateTax'])->name('tax.update');
                // Gerbang Pembayaran
                Route::get('/payment-settings',  [\App\Http\Controllers\Admin\CafeSettingController::class, 'paymentSettings'])->name('payment_settings');
                Route::post('/payment-settings', [\App\Http\Controllers\Admin\CafeSettingController::class, 'updatePaymentSettings'])->name('payment_settings.update');
            });
        });

        // HR (Admin + HR + Owner)
        Route::middleware(\App\Http\Middleware\AdminMiddleware::class.':hr,owner')->group(function() {
            Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
            Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
            Route::post('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
            Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
            Route::get('/attendances', [AdminController::class, 'attendances'])->name('admin.attendances.index');
            Route::delete('/attendances/{attendance}', [AdminController::class, 'destroyAttendance'])->name('admin.attendances.destroy');
            Route::delete('/attendances/{attendance}/proof', [AdminController::class, 'destroyAttendanceProof'])->name('admin.attendances.destroy_proof');
            Route::post('/attendances/bulk-proof-destroy', [AdminController::class, 'bulkDestroyAttendanceProof'])->name('admin.attendances.bulk_destroy_proof');
            Route::post('/users/{user}/verify-face', [AdminController::class, 'verifyFace'])->name('admin.users.verify_face');

            
            // Advanced HR Module
            Route::get('/hr/salary-components', [\App\Http\Controllers\HrAdvancedController::class, 'components'])->name('admin.hr.components');
            Route::post('/hr/salary-components', [\App\Http\Controllers\HrAdvancedController::class, 'storeComponent'])->name('admin.hr.components.store');
            Route::delete('/hr/salary-components/{id}', [\App\Http\Controllers\HrAdvancedController::class, 'destroyComponent'])->name('admin.hr.components.destroy');

            Route::get('/hr/shifts', [\App\Http\Controllers\HrAdvancedController::class, 'shifts'])->name('admin.hr.shifts');
            Route::post('/hr/shifts', [\App\Http\Controllers\HrAdvancedController::class, 'storeShift'])->name('admin.hr.shifts.store');
            Route::put('/hr/shifts/{id}', [\App\Http\Controllers\HrAdvancedController::class, 'updateShift'])->name('admin.hr.shifts.update');
            Route::delete('/hr/shifts/{id}', [\App\Http\Controllers\HrAdvancedController::class, 'destroyShift'])->name('admin.hr.shifts.destroy');

            Route::get('/hr/schedules', [\App\Http\Controllers\HrAdvancedController::class, 'schedules'])->name('admin.hr.schedules');
            Route::post('/hr/schedules', [\App\Http\Controllers\HrAdvancedController::class, 'storeSchedule'])->name('admin.hr.schedules.store');
            Route::post('/hr/schedules/auto-generate', [\App\Http\Controllers\HrAdvancedController::class, 'autoGenerateSchedule'])->name('admin.hr.schedules.auto');

            Route::get('/hr/leaves', [\App\Http\Controllers\HrAdvancedController::class, 'leaves'])->name('admin.hr.leaves');
            Route::post('/hr/leaves/{id}/approve', [\App\Http\Controllers\HrAdvancedController::class, 'approveLeave'])->name('admin.hr.leaves.approve');
            Route::post('/hr/leaves/{id}/reject', [\App\Http\Controllers\HrAdvancedController::class, 'rejectLeave'])->name('admin.hr.leaves.reject');

            Route::get('/hr/payroll', [\App\Http\Controllers\HrAdvancedController::class, 'payroll'])->name('admin.hr.payroll');
            Route::post('/hr/payroll/generate', [\App\Http\Controllers\HrAdvancedController::class, 'generatePayroll'])->name('admin.hr.payroll.generate');
        });


        // Inventory Management (Admin + Inventory + Kitchen)
        Route::middleware(\App\Http\Middleware\AdminMiddleware::class.':inventory,kitchen')->group(function() {
            Route::get('/ingredients', [AdminController::class, 'ingredients'])->name('admin.ingredients.index');
            Route::post('/ingredients', [AdminController::class, 'storeIngredient'])->name('admin.ingredients.store');
            Route::post('/ingredients/{ingredient}', [AdminController::class, 'updateIngredient'])->name('admin.ingredients.update');
            Route::delete('/ingredients/{ingredient}', [AdminController::class, 'destroyIngredient'])->name('admin.ingredients.destroy');

            Route::resource('suppliers', SupplierController::class)->names('admin.suppliers');
            Route::resource('purchase-orders', PurchaseOrderController::class)->names('admin.purchase_orders');
            Route::post('purchase-orders/{purchase_order}/status', [PurchaseOrderController::class, 'updateStatus'])->name('admin.purchase_orders.status');
            
            Route::resource('goods-receipts', GoodsReceiptController::class)->names('admin.goods_receipts');
            
            Route::resource('stock-opnames', StockOpnameController::class)->names('admin.stock_opnames');
            Route::post('stock-opnames/{stock_opname}/approve', [StockOpnameController::class, 'approve'])->name('admin.stock_opnames.approve');
            
            Route::resource('stock-transfers', StockTransferController::class)->names('admin.stock_transfers');
            
            Route::resource('stock-mutations', StockMutationController::class)->only(['index'])->names('admin.stock_mutations');
            
            Route::resource('wastes', WasteController::class)->names('admin.wastes');
            Route::resource('recipes', RecipeController::class)->names('admin.recipes');
        });

        // Finance (Admin + Finance + HR + Owner)
        Route::middleware(\App\Http\Middleware\AdminMiddleware::class.':finance,hr,owner')->group(function() {
            Route::get('finance', [\App\Http\Controllers\FinanceController::class, 'dashboard'])->name('admin.finance.dashboard');
            Route::get('finance/expenses', [\App\Http\Controllers\FinanceController::class, 'expenses'])->name('admin.finance.expenses');
            Route::post('finance/expenses', [\App\Http\Controllers\FinanceController::class, 'storeExpense'])->name('admin.finance.expenses.store');
            Route::get('finance/report', [\App\Http\Controllers\FinanceController::class, 'report'])->name('admin.finance.report');
        });
    });
});

// QRIS Callback (API/Webhook)
<<<<<<< HEAD
Route::post('/payment/qris/callback', [PosController::class, 'callbackQris'])->middleware('throttle:webhooks')->name('qris.callback');
=======
Route::post('/payment/qris/callback', [PosController::class, 'callbackQris'])->name('qris.callback');
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70

// Self-Service Customer routes
Route::prefix('self-service')->group(function () {
    Route::get('/table/{qr_token}', [\App\Http\Controllers\SelfServiceController::class, 'showMenu'])->name('self-service.table');
<<<<<<< HEAD
    Route::post('/order', [\App\Http\Controllers\SelfServiceController::class, 'storeOrder'])->middleware('throttle:orders')->name('self-service.order.store');
    Route::get('/simulation/{order_reference}', [\App\Http\Controllers\SelfServiceController::class, 'showSimulation'])->name('self-service.simulation');
    Route::get('/payment-success/{order_reference}', [\App\Http\Controllers\SelfServiceController::class, 'paymentSuccess'])->name('self-service.payment-success');
    Route::get('/payment-pending/{order_reference}', [\App\Http\Controllers\SelfServiceController::class, 'paymentPending'])->name('self-service.payment-pending');
    Route::post('/midtrans-webhook', [\App\Http\Controllers\SelfServiceController::class, 'midtransCallback'])->middleware('throttle:webhooks')->name('self-service.midtrans-webhook');
=======
    Route::post('/order', [\App\Http\Controllers\SelfServiceController::class, 'storeOrder'])->name('self-service.order.store');
    Route::get('/simulation/{order_reference}', [\App\Http\Controllers\SelfServiceController::class, 'showSimulation'])->name('self-service.simulation');
    Route::get('/payment-success/{order_reference}', [\App\Http\Controllers\SelfServiceController::class, 'paymentSuccess'])->name('self-service.payment-success');
    Route::get('/payment-pending/{order_reference}', [\App\Http\Controllers\SelfServiceController::class, 'paymentPending'])->name('self-service.payment-pending');
    Route::post('/midtrans-webhook', [\App\Http\Controllers\SelfServiceController::class, 'midtransCallback'])->name('self-service.midtrans-webhook');
>>>>>>> d0249b4929156cbfb9bc7856996342536ace9f70
});
