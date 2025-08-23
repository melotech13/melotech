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
        Schema::create('crop_progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Progress update session
            $table->string('session_id')->unique(); // Unique identifier for each update session
            $table->date('update_date'); // Date when the update was completed
            $table->enum('update_method', ['questions', 'images']); // How the farmer updated progress
            
            // Question-based updates
            $table->json('question_answers')->nullable(); // Store answers to guided questions
            
            // Image-based updates
            $table->json('selected_images')->nullable(); // Store selected plant condition images
            
            // Calculated progress
            $table->integer('calculated_progress')->default(0); // Progress calculated from answers/images
            $table->text('progress_notes')->nullable(); // Additional notes from farmer
            
            // Next update scheduling
            $table->date('next_update_date'); // When the next set of questions should appear
            
            // Status
            $table->enum('status', ['completed', 'pending'])->default('completed');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['farm_id', 'update_date']);
            $table->index(['user_id', 'next_update_date']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_progress_updates');
    }
};
