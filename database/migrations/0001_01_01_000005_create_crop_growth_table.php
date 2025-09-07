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
        Schema::create('crop_growth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');
            
            // Growth Stage Information
            $table->enum('current_stage', ['seedling', 'vegetative', 'flowering', 'fruiting', 'harvest'])->default('seedling');
            $table->integer('stage_progress')->default(0); // 0-100 percentage within current stage
            $table->integer('overall_progress')->default(0); // 0-100 percentage of total growth cycle
            
            // Growth tracking
            $table->date('last_updated');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['farm_id', 'current_stage']);
            $table->index('last_updated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_growth');
    }
};
