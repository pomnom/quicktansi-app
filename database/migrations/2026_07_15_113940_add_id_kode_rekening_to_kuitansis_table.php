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
            $table->unsignedBigInteger('id_kode_rekening')->nullable()->after('id_akun');
            $table->foreign('id_kode_rekening')->references('id')->on('kode_rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kuitansis', function (Blueprint $table) {
            $table->dropForeign(['id_kode_rekening']);
            $table->dropColumn('id_kode_rekening');
        });
    }
};
