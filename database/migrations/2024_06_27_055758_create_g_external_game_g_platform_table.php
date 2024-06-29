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
        Schema::create('g_external_game_g_platform', function (Blueprint $table) {
            $table->id();
            $table->foreignId('g_external_game_id')->constrained('g_external_games');
            $table->foreignId('g_platform_id')->constrained('g_platforms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_external_game_g_platform');
    }
};
