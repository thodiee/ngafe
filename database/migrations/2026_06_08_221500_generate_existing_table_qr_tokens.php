<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Table;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void {
        // Generate tokens for existing tables
        foreach (Table::whereNull('qr_token')->orWhere('qr_token', '')->get() as $table) {
            $table->qr_token = Str::random(32);
            $table->save();
        }
    }
    public function down(): void {
        // No action needed
    }
};
