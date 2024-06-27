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
        Schema::create('g_websites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('origin_id');
            $table->enum('category', [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18])->nullable();
            $table->string('checksum');
            $table->boolean('trusted')->default(false);
            $table->string('url');
            $table->foreignId('websiteable_id')->nullable();
            $table->string('websiteable_type');
            $table->timestamps();

            $table->unique(['origin_id', 'websiteable_id', 'websiteable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_websites');
    }
};
