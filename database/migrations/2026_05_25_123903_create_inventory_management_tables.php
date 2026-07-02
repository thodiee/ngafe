<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifikasi tabel ingredients untuk membedakan stok gudang dan dapur
        Schema::table('ingredients', function (Blueprint $table) {
            $table->renameColumn('stock', 'stock_gudang');
            $table->integer('stock_dapur')->default(0)->after('stock_gudang');
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->date('po_date');
            $table->date('expected_date')->nullable();
            $table->enum('status', ['draft', 'sent', 'partial', 'completed', 'cancelled'])->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('gr_number')->unique();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->date('receipt_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->integer('quantity_received');
            $table->decimal('unit_price', 15, 2)->default(0); // For HPP/FIFO
            $table->timestamps();
        });

        // Tabel Batches untuk FIFO
        Schema::create('ingredient_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->foreignId('goods_receipt_item_id')->nullable()->constrained('goods_receipt_items')->onDelete('cascade');
            $table->integer('initial_quantity');
            $table->integer('current_quantity');
            $table->decimal('unit_price', 15, 2);
            $table->date('batch_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number')->unique();
            $table->date('opname_date');
            $table->enum('location', ['gudang', 'dapur']);
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->integer('system_stock');
            $table->integer('actual_stock');
            $table->integer('difference');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->date('transfer_date');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->integer('quantity'); // Jumlah yang ditransfer dari gudang ke dapur
            $table->timestamps();
        });

        Schema::create('wastes', function (Blueprint $table) {
            $table->id();
            $table->date('waste_date');
            $table->enum('location', ['gudang', 'dapur']);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('waste_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_id')->constrained('wastes')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });

        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->enum('location', ['gudang', 'dapur']);
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->string('reference_type')->nullable(); // e.g. GoodsReceipt, POSOrder, StockTransfer, StockOpname, Waste
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
        Schema::dropIfExists('waste_items');
        Schema::dropIfExists('wastes');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('ingredient_batches');
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
        
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('stock_dapur');
            $table->renameColumn('stock_gudang', 'stock');
        });
    }
};
