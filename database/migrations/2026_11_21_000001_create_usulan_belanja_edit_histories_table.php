<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usulan_belanja_edit_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('usulan_belanja_id')->constrained('usulan_belanjas')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi', 20);
            $table->json('sebelum')->nullable();
            $table->json('sesudah')->nullable();
            $table->timestamps();
            $table->index(['usulan_belanja_id', 'created_at'], 'usulan_edit_history_usulan_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usulan_belanja_edit_histories');
    }
};
