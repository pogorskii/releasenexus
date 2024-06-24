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
        Schema::create('g_imageables', function (Blueprint $table) {
            $table->id();
            $table->string('g_image_id');
            $table->foreign('g_image_id')->references('image_id')->on('g_images');
            $table->foreignId('imageable_id')->nullable();
            $table->string('imageable_type');
            $table->string('collection')->nullable();
            $table->timestamps();

            $table->index(['imageable_id', 'imageable_type', 'g_image_id'], 'g_imageables_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_imageables');
    }
};
