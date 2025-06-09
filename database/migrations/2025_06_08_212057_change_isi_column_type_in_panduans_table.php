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
        Schema::table('panduans', function (Blueprint $table) {
            $table->longText('isi')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panduans', function (Blueprint $table) {
            $table->text('isi')->change(); // Ubah sesuai tipe sebelumnya (string/text)
        });
    }
};
