<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->foreignId('payroll_run_id')->constrained('payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('basic_amount', 12, 2)->default(0);
            $table->decimal('extra_allowance_total', 12, 2)->default(0);
            $table->decimal('deduction_total', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->string('slip_no')->unique();
            $table->timestamps();

            $table->unique(['payroll_run_id', 'employee_id'], 'salary_record_run_employee_unique');
            $table->index(['subscriber_id', 'year', 'month'], 'salary_record_subscriber_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_records');
    }
};
