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
        Schema::table('idp_rekomendasis', function (Blueprint $table) {
            $table->float('nilai_akhir_soft')->nullable()->after('deskripsi_rekomendasi');
            $table->float('nilai_akhir_hard')->nullable()->after('nilai_akhir_soft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idp_rekomendasis', function (Blueprint $table) {
            $table->dropColumn(['nilai_akhir_soft', 'nilai_akhir_hard']);
        });
    }
};
