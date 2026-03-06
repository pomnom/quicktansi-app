<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rekanans', function (Blueprint $table) {
            $table->string('instansi')->nullable()->after('nama_pemilik_rekening');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->string('instansi')->nullable()->after('status');
        });

        Schema::table('kuitansis', function (Blueprint $table) {
            $table->string('instansi')->nullable()->after('nip_pptk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekanans', function (Blueprint $table) {
            $table->dropColumn('instansi');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('instansi');
        });

        Schema::table('kuitansis', function (Blueprint $table) {
            $table->dropColumn('instansi');
        });
    }
};
