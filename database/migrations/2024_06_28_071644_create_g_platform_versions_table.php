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
        Schema::create('g_platform_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('g_platform_id')->nullable()->constrained('g_platforms');
            $table->uuid('checksum');
            $table->string('connectivity')->nullable();
            $table->string('cpu')->nullable();
            $table->string('graphics')->nullable();
            $table->string('media')->nullable();
            $table->string('memory')->nullable();
            $table->string('name');
            $table->string('os')->nullable();
            $table->string('output')->nullable();
            $table->string('resolutions')->nullable();
            $table->string('slug');
            $table->string('sound')->nullable();
            $table->string('storage')->nullable();
            $table->text('summary')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_platform_versions');
    }
};
