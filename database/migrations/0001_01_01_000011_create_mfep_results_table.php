<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mfep_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mfep_calculation_id')->constrained('mfep_calculations')->cascadeOnDelete();
            $table->foreignId('alternative_id')->constrained('alternatives')->cascadeOnDelete();
            $table->decimal('raw_score', 12, 4)->nullable();
            $table->decimal('weighted_score', 12, 4);
            $table->unsignedInteger('rank')->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['mfep_calculation_id', 'alternative_id']);
            $table->index(['mfep_calculation_id', 'rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfep_results');
    }
};
