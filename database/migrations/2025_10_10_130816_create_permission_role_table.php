<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Antes de crear, verificamos si existe para evitar conflictos
        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->id();

                // Ambas deben ser del mismo tipo que la tabla de referencia
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('permission_id');

                // Claves foráneas con referencias correctas
                $table->foreign('role_id')
                      ->references('id')
                      ->on('roles')
                      ->onDelete('cascade');

                $table->foreign('permission_id')
                      ->references('id')
                      ->on('permissions')
                      ->onDelete('cascade');

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
