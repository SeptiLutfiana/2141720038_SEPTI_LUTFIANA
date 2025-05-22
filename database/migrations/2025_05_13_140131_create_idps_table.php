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
        Schema::create('idps', function (Blueprint $table) {
            $table->increments('id_idp');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_mentor')->nullable(); // nullable jika belum ditentukan
            $table->foreign('id_mentor')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('id_supervisor')->nullable();
            $table->foreign('id_supervisor')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('id_semester');
            $table->foreign('id_semester')->references('id_semester')->on('semesters')->onDelete('cascade');
            $table->string('proyeksi_karir');
            $table->date('waktu_mulai');
            $table->date('waktu_selesai');
            $table->enum('status_approval_mentor', ['Menunggu Persetujuan', 'Disetujui', 'Ditolak'])->default('Menunggu Persetujuan');
            $table->enum('status_pengajuan_idp', ['Menunggu Persetujuan', 'Revisi', 'Disetujui', 'Tidak Disetujui'])->default('Menunggu Persetujuan');
            $table->string('saran_idp')->nullable();
            $table->string('deskripsi_idp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idps');
    }
};
