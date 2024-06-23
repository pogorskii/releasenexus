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
        Schema::create('g_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('origin_id');
            $table->boolean('alpha_channel')->default(false);
            $table->boolean('animated')->default(false);
            $table->string('checksum');
            $table->unsignedInteger('height')->nullable();
            $table->string('image_id')->unique();
            $table->string('url');
            $table->unsignedInteger('width')->nullable();
            $table->timestamps();
            $table->index(['origin_id', 'image_id'], 'g_images_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_images');
    }
};
