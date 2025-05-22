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
            $table->enum('status_pengerjaan', ['Menunggu Tindakan', 'Sedang Dikerjakan', 'Selesai'])->nullable()->after('status_pengajuan_idp');
            $table->boolean('is_template')->default(false)->after('status_pengerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idps', function (Blueprint $table) {
            $table->dropColumn('status_pengerjaan');
            $table->dropColumn('is_template');
        });
    }
};
