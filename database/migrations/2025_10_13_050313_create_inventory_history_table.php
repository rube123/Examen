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
     Schema::create('inventory_history', function (Blueprint $table) {
            $table->id();

            // Coincide exactamente con mediumint(8) unsigned de la tabla inventory
            $table->unsignedMediumInteger('inventory_id');

            // Estado de la copia
            $table->enum('estado', ['Perfecto', 'Dañado', 'Perdido'])->default('Perfecto');

            // Observaciones opcionales
            $table->text('observaciones')->nullable();

            // Fecha del registro
            $table->timestamp('fecha_evento')->useCurrent();

            // Relación con inventory
            $table->foreign('inventory_id')
                ->references('inventory_id')
                ->on('inventory')
                ->onDelete('cascade');
        });

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_history');
    }
};
