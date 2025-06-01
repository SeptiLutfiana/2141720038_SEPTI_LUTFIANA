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
        Schema::table('idp_kompetensi_pengerjaans', function (Blueprint $table) {
            $table->timestamps(); // menambahkan created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idp_kompetensi_pengerjaans', function (Blueprint $table) {
            $table->dropTimestamps(); // rollback jika ingin hapus kolom timestamps

        });
    }
};
