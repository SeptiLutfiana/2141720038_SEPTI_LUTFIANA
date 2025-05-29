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
            $table->unsignedInteger('id_jenjang')->nullable()->after('deskripsi_idp');
            $table->foreign('id_jenjang')->references('id_jenjang')->on('jenjangs')->onDelete('set null');
            $table->unsignedInteger('id_jabatan')->nullable()->after('id_jenjang');
            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatans')->onDelete('set null');
            $table->unsignedInteger('id_angkatanpsp')->nullable()->after('id_jabatan');
            $table->foreign('id_angkatanpsp')->references('id_angkatanpsp')->on('angkatan_psps')->onDelete('set null');
            $table->unsignedInteger('id_divisi')->nullable()->after('id_angkatanpsp');
            $table->foreign('id_divisi')->references('id_divisi')->on('divisis')->onDelete('set null');
            $table->unsignedInteger('id_penempatan')->nullable()->after('id_divisi');
            $table->foreign('id_penempatan')->references('id_penempatan')->on('penempatans')->onDelete('set null');
            $table->unsignedInteger('id_LG')->nullable()->after('id_penempatan');
            $table->foreign('id_LG')->references('id_LG')->on('learning_groups')->onDelete('set null');
            $table->unsignedBigInteger('id_semester')->nullable()->after('id_LG');
            $table->foreign('id_semester')->references('id_semester')->on('semesters')->onDelete('set null');
            $table->boolean('is_open')->default(true)->after('id_semester'); // Sesuaikan posisi after
            $table->integer('max_applies')->nullable()->after('is_open'); // Kuota maksimal apply (null = tak terbatas)
            $table->integer('current_applies')->default(0)->after('max_applies'); // Jumlah yang sudah apply

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idps', function (Blueprint $table) {
            $table->dropForeign(['id_jenjang']);
            $table->dropForeign(['id_jabatan']);
            $table->dropForeign(['id_angkatanpsp']); // Sesuaikan dengan yang Anda gunakan
            $table->dropForeign(['id_divisi']);
            $table->dropForeign(['id_penempatan']);
            $table->dropForeign(['id_LG']);
            $table->dropForeign(['id_semester']);

            // Kemudian drop kolomnya
            $table->dropColumn([
                'id_jenjang',
                'id_jabatan',
                'id_angkatanpsp', // Atau 'angkatan_psp' jika itu string
                'id_divisi',
                'id_penempatan',
                'id_LG',
                'id_semester',
                'is_open',
                'max_applies',
                'current_applies',
            ]);
        });
    }
};
