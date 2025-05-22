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
            $table->enum('hasil_rekomendasi', ['Disarankan', 'Disarankan dengan Pengembangan', 'Tidak Disarankan', 'Menunggu Hasil'])
                ->default('Menunggu Hasil')
                ->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idp_rekomendasis', function (Blueprint $table) {
            $table->enum('hasil_rekomendasi', ['Disarankan', 'Disarankan dengan Pengembangan', 'Tidak Disarankan', 'Menunggu Hasil'])
            ->default('Disarankan')
            ->change();
        });
    }
};
