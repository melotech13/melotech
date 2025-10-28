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
        Schema::create('nutrient_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Input data
            $table->decimal('nitrogen', 8, 2);
            $table->decimal('phosphorus', 8, 2);
            $table->decimal('potassium', 8, 2);
            $table->decimal('soil_ph', 4, 2);
            $table->decimal('soil_moisture', 5, 2);
            $table->enum('growth_stage', ['seedling', 'vegetative', 'flowering', 'fruiting', 'harvest']);
            
            // AI Analysis results
            $table->text('nutrient_status')->nullable();
            $table->text('deficiency_detection')->nullable();
            $table->text('ai_recommendations')->nullable();
            $table->text('stage_advisory')->nullable();
            $table->json('detailed_analysis')->nullable();
            
            // Metadata
            $table->timestamp('analysis_date');
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('analysis_date');
            $table->index('growth_stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrient_analyses');
    }
};
