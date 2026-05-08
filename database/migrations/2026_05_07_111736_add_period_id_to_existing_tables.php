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
        // 1. surveyor_assignments
        Schema::table('surveyor_assignments', function (Blueprint $table) {
            $table->dropForeign(['surveyor_id']);
            $table->dropForeign(['alternative_id']);
            $table->dropUnique(['surveyor_id', 'alternative_id']);

            // Tambah period_id
            $table->foreignId('period_id')
                ->nullable()
                ->after('id')
                ->constrained('assessment_periods')
                ->cascadeOnDelete();

            // Pasang kembali FK yang tadi dilepas
            $table->foreign('surveyor_id')->references('id')->on('surveyors')->cascadeOnDelete();
            $table->foreign('alternative_id')->references('id')->on('alternatives')->cascadeOnDelete();

            // Unique constraint baru
            $table->unique(['period_id', 'surveyor_id', 'alternative_id']);
        });

        // 2. assessments
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['surveyor_id']);
            $table->dropForeign(['alternative_id']);
            $table->dropForeign(['sub_criteria_id']);
            $table->dropUnique(['surveyor_id', 'alternative_id', 'sub_criteria_id']);

            // Tambah period_id
            $table->foreignId('period_id')
                ->nullable()
                ->after('id')
                ->constrained('assessment_periods')
                ->cascadeOnDelete();

            // Pasang kembali FK yang tadi dilepas
            $table->foreign('surveyor_id')->references('id')->on('surveyors')->cascadeOnDelete();
            $table->foreign('alternative_id')->references('id')->on('alternatives')->cascadeOnDelete();
            $table->foreign('sub_criteria_id')->references('id')->on('sub_criteria')->cascadeOnDelete();

            // Unique constraint baru - nama custom karena MySQL limit 64 karakter
            $table->unique(['period_id', 'surveyor_id', 'alternative_id', 'sub_criteria_id'], 'assessments_period_surveyor_alt_subcrit_unique');
        });

        // 3. mfep_calculations
        Schema::table('mfep_calculations', function (Blueprint $table) {
            $table->foreignId('period_id')
                ->nullable()
                ->after('id')
                ->constrained('assessment_periods')
                ->cascadeOnDelete();

            $table->index('period_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback mfep_calculations
        Schema::table('mfep_calculations', function (Blueprint $table) {
            $table->dropIndex(['period_id']);
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');
        });

        // Rollback assessments
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropUnique('assessments_period_surveyor_alt_subcrit_unique');
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');

            // Kembalikan unique constraint lama
            $table->unique(['surveyor_id', 'alternative_id', 'sub_criteria_id']);
        });

        // Rollback surveyor_assignments
        Schema::table('surveyor_assignments', function (Blueprint $table) {
            $table->dropUnique(['period_id', 'surveyor_id', 'alternative_id']);
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');

            // Kembalikan unique constraint lama
            $table->unique(['surveyor_id', 'alternative_id']);
        });
    }
};