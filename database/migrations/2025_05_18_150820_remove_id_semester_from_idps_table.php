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
        Schema::table('idps', function (Blueprint $table) {
            // Hapus foreign key constraint dulu
            $table->dropForeign(['id_semester']);
            // Hapus kolomnya
            $table->dropColumn('id_semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idps', function (Blueprint $table) {
            $table->unsignedBigInteger('id_semester');
            $table->foreign('id_semester')->references('id_semester')->on('semesters')->onDelete('cascade');
        });
    }
};
