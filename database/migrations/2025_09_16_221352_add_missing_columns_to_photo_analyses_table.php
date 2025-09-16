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
            // Add missing columns that are referenced in the model and controller
            if (!Schema::hasColumn('photo_analyses', 'identified_condition')) {
                $table->string('identified_condition')->nullable()->after('identified_type');
            }
            
            if (!Schema::hasColumn('photo_analyses', 'condition_key')) {
                $table->string('condition_key')->nullable()->after('identified_condition');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_analyses', function (Blueprint $table) {
            if (Schema::hasColumn('photo_analyses', 'identified_condition')) {
                $table->dropColumn('identified_condition');
            }
            
            if (Schema::hasColumn('photo_analyses', 'condition_key')) {
                $table->dropColumn('condition_key');
            }
        });
    }
};