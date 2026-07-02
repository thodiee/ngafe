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
        // Add salary settings to users table
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('base_salary', 15, 2)->default(0)->after('role');
            $table->decimal('daily_allowance', 15, 2)->default(0)->after('base_salary');
            $table->decimal('lateness_penalty', 15, 2)->default(0)->after('daily_allowance');
        });

        // Leaves table
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('annual'); // annual, sick, unpaid
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('hr_notes')->nullable();
            $table->timestamps();
        });

        // Salary components (Bonuses/Deductions per month)
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // bonus, deduction
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->integer('applied_month');
            $table->integer('applied_year');
            $table->timestamps();
        });

        // Payrolls table
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('period_month');
            $table->integer('period_year');
            $table->integer('present_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('total_allowance', 15, 2)->default(0);
            $table->decimal('total_deduction', 15, 2)->default(0);
            $table->decimal('total_bonus', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->string('status')->default('draft'); // draft, paid
            $table->timestamps();
            
            // Prevent duplicate payroll for same user and period
            $table->unique(['user_id', 'period_month', 'period_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('salary_components');
        Schema::dropIfExists('leaves');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['base_salary', 'daily_allowance', 'lateness_penalty']);
        });
    }
};
