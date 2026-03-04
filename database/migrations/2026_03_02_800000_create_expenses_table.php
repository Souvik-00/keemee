<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->date('expense_date');
            $table->string('category', 100);
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['subscriber_id', 'expense_date'], 'expenses_subscriber_date_idx');
            $table->index(['subscriber_id', 'customer_id', 'site_id'], 'expenses_owner_idx');
            $table->index(['subscriber_id', 'category'], 'expenses_category_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
