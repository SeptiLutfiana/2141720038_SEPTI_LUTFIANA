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
        Schema::create('panduan_roles', function (Blueprint $table) {
            $table->bigIncrements('id_panduan_role'); // primary key
            $table->unsignedBigInteger('id_panduan'); // foreign key ke panduan
            $table->unsignedInteger('id_role'); // menyesuaikan dengan roles.id_role

            // definisikan foreign key
            $table->foreign('id_panduan')->references('id_panduan')->on('panduans')->onDelete('cascade');
            $table->foreign('id_role')->references('id_role')->on('roles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panduan_roles');
    }
};
