<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pagu_anggarans', function (Blueprint $table) {
            $table->dropUnique('pagu_anggaran_tahun_rekening_unique');
            $table->unique(['tahun_anggaran_id', 'id_kode_rekening_belanja', 'jenis_sumber_dana'], 'pagu_anggaran_tahun_rekening_source_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pagu_anggarans', function (Blueprint $table) {
            $table->dropUnique('pagu_anggaran_tahun_rekening_source_unique');
            $table->unique(['tahun_anggaran_id', 'id_kode_rekening_belanja'], 'pagu_anggaran_tahun_rekening_unique');
        });
    }
};
