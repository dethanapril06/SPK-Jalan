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
        Schema::create('mfep_result_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mfep_result_id')->constrained('mfep_results')->cascadeOnDelete();
            $table->foreignId('criteria_id')->constrained('criteria')->cascadeOnDelete();
            $table->foreignId('sub_criteria_id')->nullable()->constrained('sub_criteria')->nullOnDelete();
            $table->foreignId('assessment_aspect_id')->nullable()->constrained('assessment_aspects')->nullOnDelete();
            $table->foreignId('assessment_id')->nullable()->constrained('assessments')->nullOnDelete();
            $table->decimal('evaluation_value', 12, 4);
            $table->decimal('weight', 12, 4);
            $table->decimal('weighted_value', 12, 4);
            $table->timestamps();

            $table->index(['mfep_result_id', 'criteria_id']);
            $table->index(['mfep_result_id', 'sub_criteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfep_result_details');
    }
};
