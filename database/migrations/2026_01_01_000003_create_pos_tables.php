<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number');
            $table->foreignId('area_id')->nullable()->constrained('areas')->onDelete('set null');
            $table->string('qr_token')->nullable();
            $table->string('status')->default('empty');
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_reference');
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->string('order_type'); // dine_in, takeaway
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->string('order_status')->default('pending');
            $table->string('gateway_transaction_id')->nullable();
            $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('order_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->string('variant_name');
            $table->string('option_name');
            $table->decimal('price_adjustment', 15, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('order_item_variants');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('areas');
    }
};