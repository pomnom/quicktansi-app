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
        Schema::create('sub_kegiatan', function (Blueprint $table) {
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
            $table->integer('id_sub_giat')->unique();
            $table->string('kode_sub_giat', 50);
            $table->string('nama_sub_giat');
            $table->decimal('nilai_anggaran', 15, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('id_giat')->references('id_giat')->on('kegiatan')->onDelete('cascade');
            $table->index(['id_sub_giat', 'tahun']);
            $table->index('kode_sub_giat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_kegiatan');
    }
};
