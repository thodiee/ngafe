<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('requirements');
            $table->string('status')->default('published');
            $table->timestamps();
        });
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_vacancy_id')->constrained('job_vacancies')->onDelete('cascade');
            $table->string('applicant_name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('cv_path');
            $table->string('status')->default('submitted');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_vacancies');
    }
};