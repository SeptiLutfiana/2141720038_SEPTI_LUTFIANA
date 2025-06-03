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
        Schema::table('idp_template_applies', function (Blueprint $table) {
            $table->unsignedBigInteger('id_mentor')->nullable()->after('applied_at');
            $table->foreign('id_mentor')->references('id')->on('users')->onDelete('set null');

            $table->unsignedInteger('id_jenjang')->nullable()->after('id_mentor');
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idp_template_applies', function (Blueprint $table) {
            $table->dropForeign(['id_mentor']);
            $table->dropColumn('id_mentor');
            $table->dropForeign(['id_jenjang']);
            $table->dropColumn('id_jenjang');

            $table->dropForeign(['id_jabatan']);
            $table->dropColumn('id_jabatan');

            $table->dropForeign(['id_angkatanpsp']);
            $table->dropColumn('id_angkatanpsp');

            $table->dropForeign(['id_divisi']);
            $table->dropColumn('id_divisi');

            $table->dropForeign(['id_penempatan']);
            $table->dropColumn('id_penempatan');

            $table->dropForeign(['id_LG']);
            $table->dropColumn('id_LG');
        });
    }
};
