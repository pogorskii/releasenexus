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
        Schema::create('g_videoables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('g_video_id')->constrained('g_videos');
            $table->morphs('videoable');
            $table->timestamps();

            $table->unique(['g_video_id', 'videoable_id', 'videoable_type'], 'g_videoables_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_videoables');
    }
};
