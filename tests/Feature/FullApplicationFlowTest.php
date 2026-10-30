<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Table;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\PosSession;
use Carbon\Carbon;

class FullApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the database with complete initial master data
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_pages_load_successfully(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/careers')->assertStatus(200);
    }

    public function test_auth_login_and_logout_flow(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@ngafe.com',
            'password' => 'password',
        ]);
        $response->assertRedirect('/admin');
        $this->assertAuthenticated();

        $logout = $this->post('/logout');
        $logout->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_self_service_customer_to_kitchen_to_waiter_flow(): void
    {
        $table = Table::first();
        $this->assertNotNull($table);

        // 1. Customer visits table menu using QR token
        $menuResponse = $this->get('/self-service/table/' . $table->qr_token);
        $menuResponse->assertStatus(200);

        // 2. Customer places an order via Self-Service (cashier payment method)
        $product = Product::first();
        $orderPayload = [
            'table_id' => $table->id,
            'customer_name' => 'Budi Santoso',
            'payment_method' => 'cashier',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'notes' => 'Less sugar',
                ]
            ]
        ];

        $orderResponse = $this->postJson(route('self-service.order.store'), $orderPayload);
        $orderResponse->assertStatus(200);
        $orderResponse->assertJson(['status' => 'success', 'payment_method' => 'cashier']);

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('unpaid', $order->payment_status);

        // 3. Customer views payment pending screen
        $pendingScreen = $this->get(route('self-service.payment-pending', $order->order_reference));
        $pendingScreen->assertStatus(200);

        // 4. Cashier logs in, opens shift, and confirms table payment
        $cashier = User::where('role', 'cashier')->first();
        $this->actingAs($cashier);

        // Open cashier shift
        $openShiftResponse = $this->post(route('pos.shift.storeOpen'), [
            'opening_cash' => 200000,
        ]);
        $openShiftResponse->assertRedirect(route('pos.menu'));

        // Confirm table payment
        $payResponse = $this->postJson(route('pos.table-payment', $order->id), [
            'payment_method' => 'cash'
        ]);
        $payResponse->assertStatus(200);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->order_status);

        // 5. Kitchen chef views order and marks it ready
        $kitchenUser = User::where('role', 'kitchen')->first();
        $this->actingAs($kitchenUser);

        $kitchenView = $this->get(route('kitchen.index'));
        $kitchenView->assertStatus(200);

        $readyResponse = $this->post(route('kitchen.ready', $order->id));
        $readyResponse->assertRedirect(route('kitchen.index'));

        $order->refresh();
        $this->assertEquals('ready', $order->order_status);

        // 6. Waiter views ready orders and serves to customer
        $waiter = User::where('role', 'waiter')->first();
        $this->actingAs($waiter);

        $waiterView = $this->get(route('waiter.index'));
        $waiterView->assertStatus(200);

        $serveResponse = $this->post(route('waiter.serve', $order->id));
        $serveResponse->assertRedirect(route('waiter.index'));

        $order->refresh();
        $this->assertEquals('completed', $order->order_status);
    }

    public function test_pos_cashier_direct_order_and_shift_cycle(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $this->actingAs($cashier);

        // Open shift
        $this->post(route('pos.shift.storeOpen'), ['opening_cash' => 150000]);

        $table = Table::first();
        $product = Product::first();

        // Direct POS order
        $posOrder = $this->post(route('pos.order.store'), [
            'table_id' => $table->id,
            'order_type' => 'dine_in',
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ]);
        $posOrder->assertRedirect();

        // POS live status polling
        $liveStatus = $this->getJson(route('pos.live-status'));
        $liveStatus->assertStatus(200);

        // Close shift
        $session = PosSession::where('user_id', $cashier->id)->where('status', 'open')->first();
        $closeShift = $this->post(route('pos.shift.storeClose'), [
            'closing_cash_actual' => 200000,
            'notes' => 'Shift ended smoothly'
        ]);
        $closeShift->assertRedirect(route('pos.shift.summary', $session->id));

        $summary = $this->get(route('pos.shift.summary', $session->id));
        $summary->assertStatus(200);
    }

    public function test_admin_and_owner_modules_load_successfully(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $routesToTest = [
            'admin.dashboard',
            'admin.orders.index',
            'admin.categories.index',
            'admin.products.index',
            'admin.variants.index',
            'admin.packages.index',
            'admin.areas.index',
            'admin.tables.index',
            'admin.reservations.index',
            'admin.customers.index',
            'admin.settings.loyalty',
            'admin.settings.birthday',
            'admin.settings.attendance',
            'admin.cafe.profile',
            'admin.cafe.units',
            'admin.cafe.payment_methods',
            'admin.cafe.tax',
            'admin.cafe.payment_settings',
            'admin.users.index',
            'admin.attendances.index',
            'admin.hr.components',
            'admin.hr.shifts',
            'admin.hr.schedules',
            'admin.hr.leaves',
            'admin.hr.payroll',
            'admin.ingredients.index',
            'admin.suppliers.index',
            'admin.purchase_orders.index',
            'admin.goods_receipts.index',
            'admin.stock_opnames.index',
            'admin.stock_transfers.index',
            'admin.stock_mutations.index',
            'admin.wastes.index',
            'admin.recipes.index',
            'admin.finance.dashboard',
            'admin.finance.expenses',
            'admin.finance.report',
            'admin.pos.sales_report',
            'admin.owner.report',
        ];

        foreach ($routesToTest as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(200, "Failed asserting route [{$routeName}] returned 200 OK.");
        }
    }

    public function test_admin_crud_operations(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // 1. Create Area & Table
        $this->post(route('admin.areas.store'), ['name' => 'Rooftop VIP'])->assertRedirect();
        $area = \App\Models\Area::where('name', 'Rooftop VIP')->first();
        $this->assertNotNull($area);

        $this->post(route('admin.tables.store'), [
            'table_number' => 'RF-01',
            'area_id' => $area->id,
        ])->assertRedirect();
        $this->assertDatabaseHas('tables', ['table_number' => 'RF-01']);

        // 2. Create Category & Product
        $this->post(route('admin.categories.store'), ['name' => 'Signature Mocktail'])->assertRedirect();
        $category = Category::where('name', 'Signature Mocktail')->first();
        $this->assertNotNull($category);

        $this->post(route('admin.products.store'), [
            'name' => 'Midnight Blue Sparkle',
            'category_id' => $category->id,
            'price' => 38000,
        ])->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'Midnight Blue Sparkle']);

        // 3. Create Supplier & Unit
        $this->post(route('admin.suppliers.store'), [
            'name' => 'PT Sumber Kopi Sejahtera',
            'contact_person' => 'Hendra',
            'phone' => '081299887766',
        ])->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['name' => 'PT Sumber Kopi Sejahtera']);

        $this->post(route('admin.cafe.units.store'), [
            'name' => 'Kilogram',
            'abbreviation' => 'kg',
        ])->assertRedirect();
        $this->assertDatabaseHas('units', ['abbreviation' => 'kg']);
    }
}
