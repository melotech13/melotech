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
            $table->string('analysis_id')->nullable()->after('analysis_date');
            $table->decimal('processing_time', 8, 2)->nullable()->after('analysis_id');
            $table->json('image_metadata')->nullable()->after('processing_time');
            $table->json('analysis_details')->nullable()->after('image_metadata');
            
            // Add indexes for better performance
            $table->index('analysis_id');
            $table->index('processing_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_analyses', function (Blueprint $table) {
            $table->dropIndex(['analysis_id']);
            $table->dropIndex(['processing_time']);
            $table->dropColumn(['analysis_id', 'processing_time', 'image_metadata', 'analysis_details']);
        });
    }
};