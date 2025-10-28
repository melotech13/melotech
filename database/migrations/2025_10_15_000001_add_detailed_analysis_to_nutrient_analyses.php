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
        Schema::table('nutrient_analyses', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('nutrient_analyses', 'nutrient_status')) {
                $table->text('nutrient_status')->nullable();
            }
            if (!Schema::hasColumn('nutrient_analyses', 'deficiency_detection')) {
                $table->text('deficiency_detection')->nullable();
            }
            if (!Schema::hasColumn('nutrient_analyses', 'ai_recommendations')) {
                $table->text('ai_recommendations')->nullable();
            }
            if (!Schema::hasColumn('nutrient_analyses', 'stage_advisory')) {
                $table->text('stage_advisory')->nullable();
            }
            if (!Schema::hasColumn('nutrient_analyses', 'detailed_analysis')) {
                $table->json('detailed_analysis')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nutrient_analyses', function (Blueprint $table) {
            $table->dropColumn('detailed_analysis');
        });
    }
};
