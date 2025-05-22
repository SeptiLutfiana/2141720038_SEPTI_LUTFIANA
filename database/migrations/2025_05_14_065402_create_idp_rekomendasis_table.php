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
        Schema::create('idp_rekomendasis', function (Blueprint $table) {
           $table->increments('id_rekomendasi'); 
           $table->unsignedInteger('id_idp'); 
           $table->foreign('id_idp')->references('id_idp')->on('idps')->onDelete('cascade'); 
           $table->enum('hasil_rekomendasi', ['Disarankan', 'Disarankan dengan Pengembangan', 'Tidak Disarankan']);
           $table->text('deskripsi_rekomendasi')->nullable(); 
           $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idp_rekomendasis');
    }
};
