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
        Schema::table('jabatans', function (Blueprint $table) {
            $table->unsignedInteger('id_jenjang')->after('keterangan')->nullable();
            $table->foreign('id_jenjang')->references('id_jenjang')->on('jenjangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            $table->dropForeign(['id_jenjang']);
            $table->dropColumn('id_jenjang');
        });
    }
};
