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
        Schema::create('angkatan_psps', function (Blueprint $table) {
            $table->increments('id_angkatanpsp');
            $table->tinyInteger('bulan');  // Kolom bulan (1-12)
            $table->year('tahun');  // Kolom tahun (tahun 4 digit)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angkatan_psps');
    }
};
