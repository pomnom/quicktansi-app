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
        Schema::table('kuitansis', function (Blueprint $table) {
            $table->string('periode_type', 20)->default('TU')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: reverting back to ENUM may truncate existing data with non-TU/GU values
        Schema::table('kuitansis', function (Blueprint $table) {
            $table->enum('periode_type', ['TU', 'GU'])->default('TU')->change();
        });
    }
};
