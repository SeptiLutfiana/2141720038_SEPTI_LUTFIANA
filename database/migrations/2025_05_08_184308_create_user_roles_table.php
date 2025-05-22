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
    {Schema::create('user_roles', function (Blueprint $table) {
        $table->id(); // id sebagai primary key (unsignedBigInteger auto increment)
        $table->unsignedBigInteger('id_user'); // menyesuaikan dengan users.id
        $table->unsignedInteger('id_role'); // menyesuaikan dengan roles.id_role
        $table->timestamps();

        // Foreign key constraints
        $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('id_role')->references('id_role')->on('roles')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
