<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('opening_time');
            $table->dateTime('closing_time')->nullable();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('closing_cash_system', 15, 2)->default(0);
            $table->decimal('closing_cash_actual', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('pos_session_id')->nullable()->constrained('pos_sessions')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['pos_session_id']);
            $table->dropColumn('pos_session_id');
        });
        Schema::dropIfExists('pos_sessions');
    }
};