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
            // Check if the column doesn't exist before adding it
            if (!Schema::hasColumn('photo_analyses', 'identified_type')) {
                $table->string('identified_type')->after('analysis_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_analyses', function (Blueprint $table) {
            if (Schema::hasColumn('photo_analyses', 'identified_type')) {
                $table->dropColumn('identified_type');
            }
        });
    }
};
