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
            // Make identified_condition nullable if it exists
            if (Schema::hasColumn('photo_analyses', 'identified_condition')) {
                $table->string('identified_condition')->nullable()->change();
            }
            
            // Make condition_key nullable if it exists
            if (Schema::hasColumn('photo_analyses', 'condition_key')) {
                $table->string('condition_key')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_analyses', function (Blueprint $table) {
            // Revert to not nullable if needed
            if (Schema::hasColumn('photo_analyses', 'identified_condition')) {
                $table->string('identified_condition')->nullable(false)->change();
            }
            
            if (Schema::hasColumn('photo_analyses', 'condition_key')) {
                $table->string('condition_key')->nullable(false)->change();
            }
        });
    }
};
