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
            $table->integer('id_sub_giat');
            $table->integer('id_akun')->unique();
            $table->string('kode_akun', 50);
            $table->string('nama_akun');
            $table->boolean('is_blokir')->default(false);
            $table->timestamps();

            $table->foreign('id_sub_giat')->references('id_sub_giat')->on('sub_kegiatan')->onDelete('cascade');
            $table->index('kode_akun');
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
