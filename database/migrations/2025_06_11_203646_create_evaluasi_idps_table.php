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
        Schema::create('evaluasi_idps', function (Blueprint $table) {
            $table->id('id_evaluasi_idp');
            $table->unsignedInteger('id_idp');
            $table->foreign('id_idp')->references('id_idp')->on('idps')->onDelete('cascade');
            $table->unsignedBigInteger('id_user'); // karyawan, mentor, atau supervisor
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->enum('jenis_evaluasi', ['onboarding', 'pasca']);
            $table->date('tanggal_evaluasi')->nullable(); // bisa otomatis atau manual
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_idps');
    }
};
