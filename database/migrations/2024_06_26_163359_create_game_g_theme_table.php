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
        Schema::create('game_g_theme', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games', 'origin_id');
            $table->foreignId('g_theme_id')->constrained('g_themes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_g_theme');
    }
};
