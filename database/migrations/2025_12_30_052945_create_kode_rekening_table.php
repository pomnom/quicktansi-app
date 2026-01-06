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
        Schema::create('kode_rekening', function (Blueprint $table) {
            $table->id();
            $table->integer('id_daerah');
            $table->year('tahun');
            $table->integer('id_unit');
            $table->integer('id_skpd');
            $table->integer('id_sub_skpd');
            $table->string('kode_sub_skpd', 50);
            $table->string('nama_sub_skpd');
            $table->integer('id_urusan');
            $table->integer('id_bidang_urusan');
            $table->integer('id_fungsi');
            $table->integer('id_sub_fungsi');
            $table->integer('id_program');
            $table->integer('id_giat');
            $table->integer('id_sub_giat');
            $table->integer('id_akun')->unique();
            $table->string('kode_akun', 50);
            $table->string('nama_akun');
            $table->decimal('nilai_anggaran', 15, 2)->default(0);
            $table->integer('id_rak_belanja');
            $table->string('distribusi', 10);
            $table->integer('id_pegawai_pa_kpa')->default(0);
            $table->boolean('is_blokir')->default(false);
            $table->timestamps();
            
            $table->foreign('id_sub_giat')->references('id_sub_giat')->on('sub_kegiatan')->onDelete('cascade');
            $table->index(['id_akun', 'tahun']);
            $table->index('kode_akun');
            $table->index('id_rak_belanja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kode_rekening');
    }
};
