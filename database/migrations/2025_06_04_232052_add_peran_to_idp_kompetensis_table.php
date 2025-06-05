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
            $table->enum('peran', ['umum', 'utama', 'kunci_core', 'kunci_bisnis', 'kunci_enabler'])->default('umum')->after('id_kompetensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idp_kompetensis', function (Blueprint $table) {
            $table->dropColumn('peran');
        });
    }
};
