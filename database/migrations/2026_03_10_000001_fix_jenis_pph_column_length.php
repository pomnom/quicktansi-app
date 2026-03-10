<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuitansis', function (Blueprint $table) {
            $table->string('jenis_pph', 5)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kuitansis', function (Blueprint $table) {
            $table->string('jenis_pph', 2)->nullable()->change();
        });
    }
};
