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
        Schema::create('g_characters', function (Blueprint $table) {
            $table->id();
            $table->json('akas')->nullable();
            $table->uuid('checksum')->unique();
            $table->string('country_name')->nullable();
            $table->text('description')->nullable();
            $table->enum('gender', [0, 1, 2])->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('species', [1, 2, 3, 4, 5])->nullable();
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_characters');
    }
};
