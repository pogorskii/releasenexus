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
        Schema::create('g_companies', function (Blueprint $table) {
            $table->id();
            $table->dateTime('change_date')->nullable();
            $table->enum('change_date_category', [0, 1, 2, 3, 4, 5, 6, 7])->nullable();
            $table->foreignId('changed_company_id')->nullable()->constrained('g_companies');
            $table->uuid('checksum');
            $table->unsignedInteger('country');
            $table->text('description')->nullable();
            $table->string('name');
            $table->foreignId('parent_company_id')->nullable()->constrained('g_companies');
            $table->string('slug')->unique();
            $table->dateTime('start_date')->nullable();
            $table->enum('start_date_category', [0, 1, 2, 3, 4, 5, 6, 7])->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_companies');
    }
};
