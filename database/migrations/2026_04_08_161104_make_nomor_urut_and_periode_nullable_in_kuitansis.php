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
            $table->integer('nomor_urut')->nullable()->default(null)->change();
            $table->string('periode_type', 10)->nullable()->default(null)->change();
            $table->integer('periode_number')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kuitansis', function (Blueprint $table) {
            $table->integer('nomor_urut')->nullable(false)->default(1)->change();
            $table->enum('periode_type', ['TU', 'GU'])->nullable(false)->default('TU')->change();
            $table->integer('periode_number')->nullable(false)->default(1)->change();
        });
    }
};
