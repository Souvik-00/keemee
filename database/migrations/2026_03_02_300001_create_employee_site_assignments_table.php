<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_site_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->date('assigned_from');
            $table->date('assigned_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['subscriber_id', 'employee_id', 'is_active'], 'esa_sub_emp_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_site_assignments');
    }
};
