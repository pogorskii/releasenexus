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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('origin_id')->unique();
            $table->float('aggregated_rating')->nullable();
            $table->unsignedInteger('aggregated_rating_count')->nullable();
            $table->json('alternative_names')->nullable();
            $table->enum('category', [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14])->nullable();
            $table->string('checksum')->nullable();
            $table->dateTime('first_release_date')->nullable();
            $table->unsignedInteger('hypes')->default(0);
            $table->string('name');
            $table->float('rating')->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->string('slug');
            $table->enum('status', [0, 2, 3, 4, 5, 6, 7, 8])->nullable();
            $table->text('storyline')->nullable();
            $table->text('summary')->nullable();
            $table->json('tags')->nullable();
            $table->float('total_rating')->default(0);
            $table->unsignedInteger('total_rating_count')->default(0);
            $table->string('url');
            $table->string('version_title')->nullable();
            $table->dateTime('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
