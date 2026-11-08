<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spj_barang_datang', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('spj_id')->constrained('spjs')->cascadeOnDelete();
            $table->date('tanggal_datang');
            $table->string('status_penerimaan', 16)->default('parsial');
            $table->text('keterangan')->nullable();
            $table->string('foto_utama');
            $table->string('foto_tambahan_2')->nullable();
            $table->string('foto_tambahan_3')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['spj_id', 'tanggal_datang']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('spj_barang_datang');
    }
};
