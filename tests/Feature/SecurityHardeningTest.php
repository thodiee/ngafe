<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use App\Models\Area;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\ProductVariantOption;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('login:127.0.0.1|test@example.com');
    }

    public function test_security_headers_are_present_on_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
        $this->assertTrue($response->headers->has('Permissions-Policy'));
    }

    public function test_login_rate_limiting_locks_out_after_five_failed_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword123',
            ]);
            $response->assertSessionHasErrors('email');
        }

        // 6th attempt should hit rate limiter lock out (HTTP 429 Too Many Requests)
        $lockoutResponse = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword123',
        ]);

        $this->assertEquals(429, $lockoutResponse->status());
    }

    public function test_unauthenticated_users_cannot_access_admin_routes(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');

        $jsonResponse = $this->getJson('/admin');
        $jsonResponse->assertStatus(401);
    }

    public function test_customer_points_cannot_be_manipulated_via_mass_assignment(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.customers.store'), [
            'name' => 'Hacker Customer',
            'phone' => '081234567890',
            'points' => 999999, // Attempted mass assignment injection
        ]);

        $customer = Customer::where('phone', '081234567890')->first();
        $this->assertNotNull($customer);
        // Default points should be 0, ignored from payload
        $this->assertEquals(0, (int)$customer->points);
    }

    public function test_self_service_order_calculates_authentic_variant_prices_from_db(): void
    {
        $area = Area::create(['name' => 'Indoor']);
        $table = Table::create([
            'table_number' => 'T-01',
            'area_id' => $area->id,
            'qr_token' => 'sample_token_123',
        ]);

        $category = Category::create(['name' => 'Coffee']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Espresso',
            'price' => 20000,
            'is_available' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Size',
            'is_required' => false,
        ]);

        $option = ProductVariantOption::create([
            'product_variant_id' => $variant->id,
            'name' => 'Large',
            'price_adjustment' => 5000,
        ]);

        // Malicious client tries to send negative price_adjustment to get free coffee
        $response = $this->postJson(route('self-service.order.store'), [
            'table_id' => $table->id,
            'customer_name' => 'Budi',
            'payment_method' => 'cashier',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'variants' => [
                        [
                            'option_name' => 'Large',
                            'variant_name' => 'Size',
                            'price_adjustment' => -99999, // Malicious override attempt
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200);

        // Check created order: total amount must be product price (20000) + verified option price (5000) = 25000 (ignoring tax/service if disabled)
        $order = \App\Models\Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertGreaterThanOrEqual(25000, (float)$order->total_amount);
    }

    public function test_qris_callback_rejects_unauthorized_token(): void
    {
        Setting::updateOrCreate(['key' => 'qris_webhook_token'], ['value' => 'secret_secure_key_123']);

        $response = $this->postJson(route('qris.callback'), [
            'transaction_id' => 'TX12345',
            'status' => 'settlement',
            'webhook_token' => 'invalid_token',
        ]);

        $response->assertStatus(401);
    }

    public function test_application_is_protected_against_sql_injection_payloads(): void
    {
        // 1. SQL Injection attempt in Login endpoint
        $sqlInjectionLogin = $this->post('/login', [
            'email' => "' OR '1'='1' --",
            'password' => "' OR '1'='1'",
        ]);
        // Must fail email validation or return invalid credentials, NEVER authenticate
        $this->assertGuest();

        // 2. SQL Injection in QR Token URL lookup
        $sqlInjectionQr = $this->get('/self-service/table/' . urlencode("' OR 1=1 -- "));
        // Should return 404 (Not Found), not bypass table lookup
        $sqlInjectionQr->assertStatus(404);

        // 3. SQL Injection in Admin Search (orders filter)
        $admin = User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);
        $this->actingAs($admin);

        $sqlInjectionSearch = $this->get('/admin/orders?search=' . urlencode("' UNION SELECT 1,2,3,4,5,6,7,8,9,10-- "));
        $sqlInjectionSearch->assertStatus(200); // Handled safely via PDO bound parameters without executing injection

        // Verify database remains intact
        $this->assertDatabaseHas('users', ['email' => 'owner@example.com']);
    }
}
