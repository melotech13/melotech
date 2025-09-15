<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing enum column
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn('land_size_unit');
        });
        
        // Add the new enum column with updated values
        Schema::table('farms', function (Blueprint $table) {
            $table->enum('land_size_unit', ['m2', 'ha'])->after('land_size');
        });
        
        // Set all existing records to ha (since they were likely acres or hectares before)
        DB::table('farms')->update([
            'land_size_unit' => 'ha'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new enum column
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn('land_size_unit');
        });
        
        // Add back the original enum column
        Schema::table('farms', function (Blueprint $table) {
            $table->enum('land_size_unit', ['acres', 'hectares'])->after('land_size');
        });
        
        // Convert ha back to acres for existing data
        DB::table('farms')->where('land_size_unit', 'ha')->update([
            'land_size' => DB::raw('land_size / 0.404686'),
            'land_size_unit' => 'acres'
        ]);
    }
};