<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_allowance_configs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->enum('allowance_type', ['food', 'night_shift', 'other']);
            $table->decimal('amount', 12, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['subscriber_id', 'site_id', 'allowance_type'], 'site_allowance_lookup_idx');
            $table->index(['subscriber_id', 'effective_from', 'effective_to'], 'site_allowance_effective_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_allowance_configs');
    }
};
