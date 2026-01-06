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
        Schema::create('rekanans', function (Blueprint $table) {
            $table->id();
            $table->string('npwp', 20)->nullable();
            $table->string('nama_perusahaan');
            $table->string('nomor_rekening');
            $table->string('bank');
            $table->string('nama_pemilik_rekening');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekanans');
    }
};
