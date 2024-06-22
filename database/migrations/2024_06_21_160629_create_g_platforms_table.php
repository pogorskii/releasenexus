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
        Schema::create('g_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('abbreviation')->nullable();
            $table->string('alternative_name')->nullable();
            $table->enum('category', [1, 2, 3, 4, 5, 6])->nullable();
            $table->string('checksum');
            $table->unsignedInteger('generation')->nullable();
            $table->string('name');
            $table->json('platform_family')->nullable();
            $table->string('slug');
            $table->text('summary')->nullable();
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_platforms');
    }
};
