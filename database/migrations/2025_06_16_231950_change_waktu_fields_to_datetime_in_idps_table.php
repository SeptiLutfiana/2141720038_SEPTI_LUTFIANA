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
            $table->dateTime('waktu_mulai')->change();
            $table->dateTime('waktu_selesai')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idps', function (Blueprint $table) {
            $table->date('waktu_mulai')->change();
            $table->date('waktu_selesai')->change();
        });
    }
};
