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
        Schema::table('evaluasi_idps', function (Blueprint $table) {
            $table->boolean('dibaca')->default(false)->after('jenis_evaluasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluasi_idps', function (Blueprint $table) {
            $table->dropColumn('dibaca');
        });
    }
};
