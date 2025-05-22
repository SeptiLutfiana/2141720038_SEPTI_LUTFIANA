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
        Schema::create('nilai_pengerjaan_idps', function (Blueprint $table) {
            $table->increments('id_nilaiPengerjaan');
            $table->unsignedInteger('id_idpKomPeng');
            $table->foreign('id_idpKomPeng')->references('id_idpKomPeng')->on('idp_kompetensi_pengerjaans')->onDelete('cascade');
            $table->enum('rating', ['1', '2', '3', '4', '5'])->nullable();
            $table->string('saran');
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_pengerjaan_idps');
    }
};
