<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuitansis', function (Blueprint $table) {
            $table->bigInteger('pph_22')->default(0)->after('pph');
            $table->bigInteger('pph_23')->default(0)->after('pph_22');
            $table->string('kode_objek_pajak_23', 20)->nullable()->after('kode_objek_pajak');
            $table->decimal('tarif_pajak_23', 5, 2)->nullable()->default(0)->after('tarif_pajak');
        });
    }

    public function down(): void
    {
        Schema::table('kuitansis', function (Blueprint $table) {
            $table->dropColumn(['pph_22', 'pph_23', 'kode_objek_pajak_23', 'tarif_pajak_23']);
        });
    }
};
