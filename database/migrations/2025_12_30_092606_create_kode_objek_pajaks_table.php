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
        Schema::create('kode_objek_pajaks', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->text('nama');
            $table->decimal('tarif', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kode_objek_pajaks');
    }
};
