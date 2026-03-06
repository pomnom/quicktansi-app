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
        Schema::table('kegiatan', function (Blueprint $table) {
            if (Schema::hasColumn('kegiatan', 'tahun')) {
                $table->dropIndex(['id_giat', 'tahun']);
            }
        });

        $kegiatanDropColumns = [
            'id_daerah',
            'tahun',
            'id_unit',
            'id_skpd',
            'id_sub_skpd',
            'kode_sub_skpd',
            'nama_sub_skpd',
            'id_urusan',
            'id_bidang_urusan',
            'id_fungsi',
            'id_sub_fungsi',
            'id_program',
            'nilai_anggaran',
        ];

        foreach ($kegiatanDropColumns as $column) {
            if (Schema::hasColumn('kegiatan', $column)) {
                Schema::table('kegiatan', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        Schema::table('sub_kegiatan', function (Blueprint $table) {
            if (Schema::hasColumn('sub_kegiatan', 'tahun')) {
                $table->dropIndex(['id_sub_giat', 'tahun']);
            }
        });

        $subKegiatanDropColumns = [
            'id_daerah',
            'tahun',
            'id_unit',
            'id_skpd',
            'id_sub_skpd',
            'kode_sub_skpd',
            'nama_sub_skpd',
            'id_urusan',
            'id_bidang_urusan',
            'id_fungsi',
            'id_sub_fungsi',
            'id_program',
            'nilai_anggaran',
        ];

        foreach ($subKegiatanDropColumns as $column) {
            if (Schema::hasColumn('sub_kegiatan', $column)) {
                Schema::table('sub_kegiatan', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        Schema::table('kode_rekening', function (Blueprint $table) {
            if (Schema::hasColumn('kode_rekening', 'tahun')) {
                $table->dropIndex(['id_akun', 'tahun']);
            }
            if (Schema::hasColumn('kode_rekening', 'id_rak_belanja')) {
                $table->dropIndex(['id_rak_belanja']);
            }
        });

        $kodeRekeningDropColumns = [
            'id_daerah',
            'tahun',
            'id_unit',
            'id_skpd',
            'id_sub_skpd',
            'kode_sub_skpd',
            'nama_sub_skpd',
            'id_urusan',
            'id_bidang_urusan',
            'id_fungsi',
            'id_sub_fungsi',
            'id_program',
            'id_giat',
            'nilai_anggaran',
            'id_rak_belanja',
            'distribusi',
            'id_pegawai_pa_kpa',
        ];

        foreach ($kodeRekeningDropColumns as $column) {
            if (Schema::hasColumn('kode_rekening', $column)) {
                Schema::table('kode_rekening', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->integer('id_daerah')->nullable()->after('id');
            $table->year('tahun')->nullable()->after('id_daerah');
            $table->integer('id_unit')->nullable()->after('tahun');
            $table->integer('id_skpd')->nullable()->after('id_unit');
            $table->integer('id_sub_skpd')->nullable()->after('id_skpd');
            $table->string('kode_sub_skpd', 50)->nullable()->after('id_sub_skpd');
            $table->string('nama_sub_skpd')->nullable()->after('kode_sub_skpd');
            $table->integer('id_urusan')->nullable()->after('nama_sub_skpd');
            $table->integer('id_bidang_urusan')->nullable()->after('id_urusan');
            $table->integer('id_fungsi')->nullable()->after('id_bidang_urusan');
            $table->integer('id_sub_fungsi')->nullable()->after('id_fungsi');
            $table->integer('id_program')->nullable()->after('id_sub_fungsi');
            $table->decimal('nilai_anggaran', 15, 2)->default(0)->after('nama_giat');
            $table->index(['id_giat', 'tahun']);
        });

        Schema::table('sub_kegiatan', function (Blueprint $table) {
            $table->integer('id_daerah')->nullable()->after('id');
            $table->year('tahun')->nullable()->after('id_daerah');
            $table->integer('id_unit')->nullable()->after('tahun');
            $table->integer('id_skpd')->nullable()->after('id_unit');
            $table->integer('id_sub_skpd')->nullable()->after('id_skpd');
            $table->string('kode_sub_skpd', 50)->nullable()->after('id_sub_skpd');
            $table->string('nama_sub_skpd')->nullable()->after('kode_sub_skpd');
            $table->integer('id_urusan')->nullable()->after('nama_sub_skpd');
            $table->integer('id_bidang_urusan')->nullable()->after('id_urusan');
            $table->integer('id_fungsi')->nullable()->after('id_bidang_urusan');
            $table->integer('id_sub_fungsi')->nullable()->after('id_fungsi');
            $table->integer('id_program')->nullable()->after('id_sub_fungsi');
            $table->decimal('nilai_anggaran', 15, 2)->default(0)->after('nama_sub_giat');
            $table->index(['id_sub_giat', 'tahun']);
        });

        Schema::table('kode_rekening', function (Blueprint $table) {
            $table->integer('id_daerah')->nullable()->after('id');
            $table->year('tahun')->nullable()->after('id_daerah');
            $table->integer('id_unit')->nullable()->after('tahun');
            $table->integer('id_skpd')->nullable()->after('id_unit');
            $table->integer('id_sub_skpd')->nullable()->after('id_skpd');
            $table->string('kode_sub_skpd', 50)->nullable()->after('id_sub_skpd');
            $table->string('nama_sub_skpd')->nullable()->after('kode_sub_skpd');
            $table->integer('id_urusan')->nullable()->after('nama_sub_skpd');
            $table->integer('id_bidang_urusan')->nullable()->after('id_urusan');
            $table->integer('id_fungsi')->nullable()->after('id_bidang_urusan');
            $table->integer('id_sub_fungsi')->nullable()->after('id_fungsi');
            $table->integer('id_program')->nullable()->after('id_sub_fungsi');
            $table->integer('id_giat')->nullable()->after('id_program');
            $table->decimal('nilai_anggaran', 15, 2)->default(0)->after('nama_akun');
            $table->integer('id_rak_belanja')->nullable()->after('nilai_anggaran');
            $table->string('distribusi', 10)->nullable()->after('id_rak_belanja');
            $table->integer('id_pegawai_pa_kpa')->default(0)->after('distribusi');
            $table->index(['id_akun', 'tahun']);
            $table->index(['id_rak_belanja']);
        });
    }
};
