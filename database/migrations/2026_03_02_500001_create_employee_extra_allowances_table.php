<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_extra_allowances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->enum('allowance_type', ['food', 'night_shift', 'other']);
            $table->decimal('amount', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['subscriber_id', 'employee_id', 'year', 'month'], 'extra_allowance_emp_period_idx');
            $table->index(['subscriber_id', 'site_id', 'year', 'month'], 'extra_allowance_site_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_extra_allowances');
    }
};
