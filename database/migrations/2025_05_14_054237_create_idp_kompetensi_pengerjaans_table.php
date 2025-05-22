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
        Schema::create('idp_kompetensi_pengerjaans', function (Blueprint $table) {
            $table->increments('id_idpKomPeng');
            $table->unsignedInteger('id_idpKom');
            $table->foreign('id_idpKom')->references('id_idpKom')->on('idp_kompetensis')->onDelete('cascade');
            $table->string('upload_hasil');
            $table->text('keterangan_hasil')->nullable(); 
            $table->enum('status_pengerjaan', [
                'Menunggu Persetujuan', 
                'Disetujui Mentor', 
                'Ditolak Mentor', 
                'Revisi Mentor'
            ])->default('Menunggu Persetujuan');
            $table->text('saran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idp_kompetensi_pengerjaans');
    }
};
