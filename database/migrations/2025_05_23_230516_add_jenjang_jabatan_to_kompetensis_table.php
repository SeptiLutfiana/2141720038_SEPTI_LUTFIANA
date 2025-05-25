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
        Schema::table('kompetensis', function (Blueprint $table) {
            $table->unsignedInteger('id_jenjang')->after('id_kompetensi')->nullable();
            $table->foreign('id_jenjang')->references('id_jenjang')->on('jenjangs')->onDelete('cascade');
            $table->unsignedInteger('id_jabatan')->after('id_jenjang')->nullable();
            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kompetensis', function (Blueprint $table) {
            $table->dropForeign(['id_jenjang']);
            $table->dropForeign(['id_jabatan']);
            $table->dropColumn(['id_jenjang', 'id_jabatan']);
        });
    }
};
