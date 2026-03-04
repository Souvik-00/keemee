<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('manager_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('visit_date');
            $table->time('in_time');
            $table->time('out_time')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['subscriber_id', 'manager_employee_id', 'visit_date'], 'site_visit_manager_date_idx');
            $table->index(['subscriber_id', 'site_id', 'visit_date'], 'site_visit_site_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
