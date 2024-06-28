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
        Schema::create('g_event_networks', function (Blueprint $table) {
            $table->id();
            $table->uuid('checksum');
            $table->foreignId('g_event_id')->nullable()->constrained('g_events');
            $table->foreignId('g_network_type_id')->constrained('g_network_types');
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_event_networks');
    }
};
