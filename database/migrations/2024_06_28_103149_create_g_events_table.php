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
        Schema::create('g_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('checksum')->unique();
            $table->text('description')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->text('live_stream_url')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->dateTime('start_time')->nullable();
            $table->string('time_zone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_events');
    }
};
