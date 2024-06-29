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
        Schema::create('g_external_games', function (Blueprint $table) {
            $table->id();
            $table->enum('category', [
                1,
                5,
                10,
                11,
                13,
                14,
                15,
                20,
                22,
                23,
                26,
                28,
                29,
                30,
                31,
                32,
                36,
                37,
                54,
                55,
            ])->nullable();
            $table->uuid('checksum');
            $table->json('countries')->nullable();
            $table->foreignId('game_id')->nullable()->constrained('games');
            $table->enum('media', [1, 2])->nullable();
            $table->string('name')->nullable();
            $table->string('uid')->index()->nullable();
            $table->text('url')->nullable();
            $table->unsignedInteger('year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_external_games');
    }
};
