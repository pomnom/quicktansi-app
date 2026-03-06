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
            $table->integer('id_giat');
            $table->integer('id_sub_giat')->unique();
            $table->string('kode_sub_giat', 50);
            $table->string('nama_sub_giat');
            $table->timestamps();

            $table->foreign('id_giat')->references('id_giat')->on('kegiatan')->onDelete('cascade');
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
