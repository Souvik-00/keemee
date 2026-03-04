<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_monthly_summaries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('present_days')->default(0);
            $table->unsignedSmallInteger('absent_days')->default(0);
            $table->decimal('attendance_percent', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['subscriber_id', 'employee_id', 'year', 'month'], 'attendance_monthly_unique');
            $table->index(['subscriber_id', 'year', 'month'], 'attendance_subscriber_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_monthly_summaries');
    }
};
