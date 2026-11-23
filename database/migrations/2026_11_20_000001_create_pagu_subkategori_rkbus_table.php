<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pagu_subkategori_rkbus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tahun_anggaran_id')->constrained('tahun_anggarans')->cascadeOnDelete();
            $table->foreignUuid('sub_kategori_rkbu_id')->references('id_sub_kategori_rkbu')->on('sub_kategori_rkbus')->cascadeOnDelete();
            $table->enum('jenis_sumber_dana', ['BLUD', 'Non BLUD']);
            $table->decimal('jumlah_pagu', 18, 2);
            $table->timestamps();
            $table->unique(['tahun_anggaran_id', 'sub_kategori_rkbu_id', 'jenis_sumber_dana'], 'pagu_subkategori_year_category_source_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagu_subkategori_rkbus');
    }
};
