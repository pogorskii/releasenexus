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
        Schema::create('g_companiables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('g_company_id')->constrained('g_companies');
            $table->morphs('companiable');
            $table->string('role');
            $table->timestamps();
            
            $table->unique(['g_company_id', 'companiable_id', 'companiable_type', 'role'], 'g_companiables_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_companiables');
    }
};
