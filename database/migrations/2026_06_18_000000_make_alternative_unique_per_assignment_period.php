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
        Schema::table('surveyor_assignments', function (Blueprint $table) {
            $table->index('period_id', 'surveyor_assignments_period_id_index');
            $table->dropUnique(['period_id', 'surveyor_id', 'alternative_id']);
            $table->unique(['period_id', 'alternative_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveyor_assignments', function (Blueprint $table) {
            $table->dropUnique(['period_id', 'alternative_id']);
            $table->unique(['period_id', 'surveyor_id', 'alternative_id']);
            $table->dropIndex('surveyor_assignments_period_id_index');
        });
    }
};
