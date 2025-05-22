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
        Schema::table('metode_belajars', function (Blueprint $table) {
            $table->renameColumn('id', 'id_metodeBelajar');

            // Tambahkan kolom baru
            $table->string('nama_metodeBelajar')->nullable();
            $table->string('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metode_belajars', function (Blueprint $table) {
            $table->renameColumn('id_metodeBelajar', 'id');
            $table->dropColumn(['nama_metodeBelajar', 'keterangan']);
        });
    }
};
