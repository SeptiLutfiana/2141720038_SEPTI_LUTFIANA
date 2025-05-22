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
            $table->renameColumn('id_divisi', 'id_kompetensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kompetensis', function (Blueprint $table) {
            $table->renameColumn('id_kompetensi', 'id_divisi');
        });
    }
};
