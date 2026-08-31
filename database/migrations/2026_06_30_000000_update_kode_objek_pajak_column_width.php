<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kuitansis', function (Blueprint $table) {
            // Change kode_objek_pajak from 20 to 255 characters
            $table->string('kode_objek_pajak', 255)->nullable()->change();
            // Change kode_objek_pajak_23 from 20 to 255 characters
            $table->string('kode_objek_pajak_23', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kuitansis', function (Blueprint $table) {
            // Revert kode_objek_pajak back to 20 characters
            $table->string('kode_objek_pajak', 20)->nullable()->change();
            // Revert kode_objek_pajak_23 back to 20 characters
            $table->string('kode_objek_pajak_23', 20)->nullable()->change();
        });
    }
};
