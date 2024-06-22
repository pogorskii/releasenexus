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
        Schema::create('g_release_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('origin_id');
            $table->enum('category', ['0', '1', '2', '3', '4', '5', '6', '7'])->nullable();
            $table->string('checksum');
            $table->dateTime('date')->nullable();
            $table->string('human')->nullable();
            $table->unsignedSmallInteger('m')->nullable();
            $table->enum('region', ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'])->nullable();
            $table->foreignId('status_id')->nullable()->constrained('g_release_date_statuses');
            $table->unsignedSmallInteger('y')->nullable();
            $table->foreignId('dateable_id')->nullable();
            $table->string('dateable_type')->nullable();
            $table->timestamps();
            $table->index('origin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_release_dates');
    }
};
