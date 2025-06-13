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
        Schema::create('bank_evaluasis', function (Blueprint $table) {
            $table->increments('id_bank_evaluasi');
            $table->enum('jenis_evaluasi', ['onboarding', 'pasca']);
            $table->enum('untuk_role', ['karyawan', 'mentor', 'supervisor']);
            $table->enum('tipe_pertanyaan', ['likert', 'esai']);
            $table->text('pertanyaan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_evaluasis');
    }
};
