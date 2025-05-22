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
        Schema::create('idp_kompetensis', function (Blueprint $table) {
            $table->increments('id_idpKom');
            $table->unsignedInteger('id_idp');
            $table->foreign('id_idp')->references('id_idp')->on('idps')->onDelete('cascade');
            $table->unsignedInteger('id_kompetensi');
            $table->foreign('id_kompetensi')->references('id_kompetensi')->on('kompetensis')->onDelete('cascade');
            $table->unsignedBigInteger('id_metode_belajar');
            $table->foreign('id_metode_belajar')->references('id_metodeBelajar')->on('metode_belajars')->onDelete('cascade');
            $table->string('sasaran');
            $table->string('aksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idp_kompetensis');
    }
};
