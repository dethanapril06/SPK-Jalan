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
        Schema::create('assessment_periods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();        
            $table->string('name');                  
            $table->year('year');                    
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();  
            $table->date('end_date')->nullable();    
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_periods');
    }
};
