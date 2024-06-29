<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('g_localizations', function (Blueprint $table) {
            $table->id();
            $table->uuid('checksum');
            $table->foreignId('game_id')->constrained('games');
            $table->string('name')->nullable();
            $table->foreignId('g_region_id')->constrained('g_regions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_localizations');
    }
};
