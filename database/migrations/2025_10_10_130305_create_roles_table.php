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
    if (Schema::hasTable('roles')) {
        return; // evita error si ya existe
    }
    Schema::create('roles', function (Blueprint $table) {
        $table->id();                            // id (bigint)
        $table->string('name')->unique();        // admin|employee|customer|public
        $table->string('display_name');          // Administrador general, Empleado...
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
