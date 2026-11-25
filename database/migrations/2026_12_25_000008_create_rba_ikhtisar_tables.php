<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rba_ikhtisar_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('section', 40);
            $table->unsignedSmallInteger('urutan');
            $table->string('kode_ikhtisar', 50)->nullable();
            $table->string('nama_ikhtisar', 500);
            $table->string('jenis_baris', 20)->default('detail');
            $table->unsignedTinyInteger('level')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['section', 'urutan'], 'rba_ikhtisar_section_order_unique');
            $table->index(['section', 'jenis_baris', 'urutan'], 'rba_ikhtisar_section_type_order_index');
        });

        Schema::create('rba_ikhtisar_mappings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ikhtisar_item_id')->constrained('rba_ikhtisar_items')->cascadeOnDelete();
            $table->foreignUuid('sub_kategori_rkbu_id')->nullable()
                ->references('id_sub_kategori_rkbu')->on('sub_kategori_rkbus')->restrictOnDelete();
            $table->foreignUuid('rekening_pendapatan_id')->nullable()
                ->references('id')->on('rekening_pendapatans')->restrictOnDelete();
            $table->enum('jenis_sumber_dana', ['BLUD', 'Non BLUD'])->nullable();
            $table->timestamps();
            $table->unique(['ikhtisar_item_id', 'sub_kategori_rkbu_id', 'jenis_sumber_dana'], 'rba_ikhtisar_sub_mapping_unique');
            $table->unique(['ikhtisar_item_id', 'rekening_pendapatan_id', 'jenis_sumber_dana'], 'rba_ikhtisar_income_mapping_unique');
            $table->index('rekening_pendapatan_id', 'rba_ikhtisar_income_account_index');
        });

        Schema::create('rba_ikhtisar_realisasis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tahun_anggaran_id')->constrained('tahun_anggarans')->cascadeOnDelete();
            $table->foreignUuid('ikhtisar_item_id')->constrained('rba_ikhtisar_items')->cascadeOnDelete();
            $table->enum('jenis_sumber_dana', ['BLUD', 'Non BLUD']);
            $table->decimal('jumlah_realisasi', 18, 2)->default(0);
            $table->text('keterangan_sumber')->nullable();
            $table->foreignUuid('created_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['tahun_anggaran_id', 'ikhtisar_item_id', 'jenis_sumber_dana'], 'rba_ikhtisar_realization_unique');
        });

        $template = require database_path('seeders/data/rba_ikhtisar_template.php');
        $now = now();

        foreach ($template as $row) {
            DB::table('rba_ikhtisar_items')->insert([
                'id' => (string) Str::uuid(),
                'section' => $row['section'],
                'urutan' => $row['row'],
                'kode_ikhtisar' => $row['code'],
                'nama_ikhtisar' => $row['name'],
                'jenis_baris' => $row['kind'],
                'level' => $row['level'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rba_ikhtisar_realisasis');
        Schema::dropIfExists('rba_ikhtisar_mappings');
        Schema::dropIfExists('rba_ikhtisar_items');
    }
};
