<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_record_components', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->foreignId('salary_record_id')->constrained('salary_records')->cascadeOnDelete();
            $table->enum('component_type', ['earning', 'deduction']);
            $table->string('component_name');
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->index(['salary_record_id', 'component_type'], 'salary_component_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_record_components');
    }
};
