<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Master Satuan (Unit of Measure)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // e.g. Gram, Kilogram, Liter, Pcs
            $table->string('abbreviation'); // e.g. gr, kg, L, pcs
            $table->timestamps();
        });

        // Metode Pembayaran
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');         // e.g. Cash, QRIS, Kartu Debit, Transfer
            $table->string('icon')->nullable(); // font-awesome class or image path
            $table->boolean('is_active')->default(true);
            $table->boolean('has_surcharge')->default(false);
            $table->decimal('surcharge_rate', 5, 2)->default(0); // Percent
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('units');
    }
};
