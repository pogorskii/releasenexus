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
        Schema::create('game_g_collection', function (Blueprint $table) {
            $table->id();
            $table->foreignId('g_collection_id')->constrained('g_collections');
            $table->foreignId('game_id')->constrained('games', 'origin_id');
            $table->boolean('main_collection')->default(false);
            $table->enum('type', [0, 1])->nullable();
            $table->timestamps();

            $table->index(['game_id', 'g_collection_id'], 'game_g_collection_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_g_collection');
    }
};
