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
        Schema::create('kuitansis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_rekening');
            $table->integer('id_akun')->nullable();
            $table->index('id_akun');
            $table->enum('periode_type', ['TU', 'GU'])->default('TU');
            $table->integer('periode_number')->default(1);
            $table->integer('nomor_urut')->default(1);
            $table->string('no_buku')->nullable();
            $table->foreignId('rekanan_id')->nullable()->constrained('rekanans')->onDelete('set null');
            $table->string('nama_penerima');
            $table->date('tanggal_kuitansi')->nullable();
            $table->date('tanggal_pemotongan')->nullable();
            $table->decimal('ppn', 10, 2)->nullable();
            $table->decimal('pph', 10, 2)->nullable();
            $table->string('jenis_pph', 2)->nullable();
            $table->string('kode_objek_pajak', 20)->nullable();
            $table->decimal('tarif_pajak', 5, 2)->nullable();
            $table->decimal('dpp', 15, 2)->nullable();
            $table->string('jenis_dokumen', 50)->default('PaymentProof');
            $table->text('untuk_pembayaran')->nullable();
            $table->bigInteger('total_akhir')->nullable();
            $table->json('rincian_item')->nullable();
            
            // Staff relations
            $table->unsignedBigInteger('pptk_1_id')->nullable();
            $table->foreign('pptk_1_id')->references('id')->on('staff')->onDelete('set null');
            
            // Staff snapshot for historical accuracy
            $table->string('nama_pengguna_anggaran')->nullable();
            $table->string('nip_pengguna_anggaran')->nullable();
            $table->string('nama_bendahara_pengeluaran')->nullable();
            $table->string('nip_bendahara_pengeluaran')->nullable();
            $table->string('nama_bendahara_barang')->nullable();
            $table->string('nip_bendahara_barang')->nullable();
            $table->string('nama_pptk')->nullable();
            $table->string('nip_pptk')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuitansis');
    }
};
