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
            $table->string('identified_condition')->nullable()->after('identified_type');
            $table->string('condition_key')->nullable()->after('identified_condition');
            $table->index('condition_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_analyses', function (Blueprint $table) {
            $table->dropIndex(['condition_key']);
            $table->dropColumn(['identified_condition', 'condition_key']);
        });
    }
};


