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
        Schema::create('idp_kompetensi_metode_belajars', function (Blueprint $table) {
            $table->increments('id_komMetode');
            $table->unsignedInteger('id_idpKom');
            $table->foreign('id_idpKom')->references('id_idpKom')->on('idp_kompetensis')->onDelete('cascade');
            $table->unsignedBigInteger('id_metodeBelajar');
            $table->foreign('id_metodeBelajar')->references('id_metodeBelajar')->on('metode_belajars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idp_kompetensi_metode_belajars');
    }
};
