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
            $table->dropIndex(['instansi']);
            $table->dropColumn('instansi');
        });
    }
};
