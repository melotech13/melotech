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
        Schema::table('photo_analyses', function (Blueprint $table) {
            $table->json('condition_scores')->nullable()->after('analysis_details');
            $table->string('model_version')->nullable()->after('condition_scores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_analyses', function (Blueprint $table) {
            $table->dropColumn(['condition_scores', 'model_version']);
        });
    }
};


