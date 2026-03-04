<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('site_code');
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['subscriber_id', 'site_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
