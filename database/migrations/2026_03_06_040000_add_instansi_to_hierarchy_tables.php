<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->string('instansi', 100)->after('id')->nullable();
            $table->index('instansi');
        });

        Schema::table('sub_kegiatan', function (Blueprint $table) {
            $table->string('instansi', 100)->after('id')->nullable();
            $table->index('instansi');
        });

        Schema::table('kode_rekening', function (Blueprint $table) {
            $table->string('instansi', 100)->after('id')->nullable();
            $table->index('instansi');
            $table->unique(['instansi', 'id_sub_giat', 'id_akun'], 'kode_rekening_instansi_sub_giat_akun_unique');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropIndex(['instansi']);
            $table->dropColumn('instansi');
        });

        Schema::table('sub_kegiatan', function (Blueprint $table) {
            $table->dropIndex(['instansi']);
            $table->dropColumn('instansi');
        });

        Schema::table('kode_rekening', function (Blueprint $table) {
            $table->dropUnique('kode_rekening_instansi_sub_giat_akun_unique');
            $table->dropIndex(['instansi']);
            $table->dropColumn('instansi');
        });
    }
};
