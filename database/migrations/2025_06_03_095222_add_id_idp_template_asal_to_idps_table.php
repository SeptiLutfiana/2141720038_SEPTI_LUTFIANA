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
            $table->unsignedInteger('id_idp_template_asal')->nullable()->after('is_template');
            $table->foreign('id_idp_template_asal')->references('id_idp')->on('idps')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idps', function (Blueprint $table) {
            $table->dropForeign(['id_idp_template_asal']);
            $table->dropColumn('id_idp_template_asal');
        });
    }
};
