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
        // Create provinces table with complete structure
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->index();
            $table->string('name');
            $table->string('region_code')->nullable();
            $table->string('region_name')->nullable();
            $table->timestamps();
        });

        // Create municipalities table with complete structure
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->index();
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // Create barangays table with complete structure
        Schema::create('barangays', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->index();
            $table->foreignId('municipality_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangays');
        Schema::dropIfExists('municipalities');
        Schema::dropIfExists('provinces');
    }
};
