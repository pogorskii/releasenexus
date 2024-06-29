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
        Schema::create('g_multiplayer_modes', function (Blueprint $table) {
            $table->id();
            $table->boolean('campaign_coop')->nullable();
            $table->uuid('checksum');
            $table->boolean('drop_in')->nullable();
            $table->foreignId('game_id')->nullable()->constrained('games');
            $table->boolean('lan_coop')->nullable();
            $table->boolean('offline_coop')->nullable();
            $table->unsignedInteger('offline_coop_max')->nullable();
            $table->unsignedInteger('offline_max')->nullable();
            $table->boolean('online_coop')->nullable();
            $table->unsignedInteger('online_coop_max')->nullable();
            $table->unsignedInteger('online_max')->nullable();
            $table->foreignId('g_platform_id')->nullable()->constrained('g_platforms');
            $table->boolean('splitscreen')->nullable();
            $table->boolean('splitscreen_online')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_multiplayer_modes');
    }
};
