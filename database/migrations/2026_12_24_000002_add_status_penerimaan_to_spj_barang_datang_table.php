<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('spj_barang_datang') && ! Schema::hasColumn('spj_barang_datang', 'status_penerimaan')) {
            Schema::table('spj_barang_datang', function (Blueprint $table) {
                $table->string('status_penerimaan', 16)->default('parsial')->after('tanggal_datang');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('spj_barang_datang') && Schema::hasColumn('spj_barang_datang', 'status_penerimaan')) {
            Schema::table('spj_barang_datang', function (Blueprint $table) {
                $table->dropColumn('status_penerimaan');
            });
        }
    }
};
