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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Farm Information
            $table->string('farm_name');
            $table->string('watermelon_variety');
            $table->date('planting_date');
            $table->decimal('field_size', 8, 2);
            $table->enum('field_size_unit', ['acres', 'hectares']);
            
            // Location Information
            $table->string('province_name');
            $table->string('city_municipality_name');
            $table->string('barangay_name')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Performance indexes
            $table->index(['province_name', 'city_municipality_name']);
            $table->index('farm_name');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
