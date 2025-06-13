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
        Schema::create('evaluasi_idp_jawabans', function (Blueprint $table) {
            $table->id('id_jawaban');
            $table->unsignedBigInteger('id_evaluasi_idp');
            $table->foreign('id_evaluasi_idp')->references('id_evaluasi_idp')->on('evaluasi_idps')->onDelete('cascade');
            $table->unsignedInteger('id_bank_evaluasi');
            $table->foreign('id_bank_evaluasi')->references('id_bank_evaluasi')->on('bank_evaluasis')->onDelete('cascade');
            $table->unsignedTinyInteger('jawaban_likert')->nullable(); // untuk pertanyaan likert
            $table->text('jawaban_esai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_idp_jawabans');
    }
};
