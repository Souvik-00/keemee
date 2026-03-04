<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->string('employee_code');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('designation')->nullable();
            $table->enum('employee_type', ['guard', 'site_manager', 'manager', 'other'])->default('other');
            $table->date('join_date')->nullable();
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['subscriber_id', 'employee_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
