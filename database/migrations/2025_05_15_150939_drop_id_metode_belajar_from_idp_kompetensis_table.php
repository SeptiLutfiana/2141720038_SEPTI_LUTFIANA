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
        Schema::table('idp_kompetensis', function (Blueprint $table) {
            $table->dropForeign(['id_metode_belajar']);
            $table->dropColumn('id_metode_belajar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idp_kompetensis', function (Blueprint $table) {
            $table->unsignedBigInteger('id_metode_belajar')->nullable();
            $table->foreign('id_metode_belajar')->references('id_metodeBelajar')->on('metode_belajars')->onDelete('set null');
        });
    }
};
