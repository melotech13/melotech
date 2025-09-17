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
        Schema::table('farms', function (Blueprint $table) {
            $table->renameColumn('field_size', 'land_size');
            $table->renameColumn('field_size_unit', 'land_size_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->renameColumn('land_size', 'field_size');
            $table->renameColumn('land_size_unit', 'field_size_unit');
        });
    }
};
