<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('customer_id'); // sakila.customer.customer_id (SMALLINT UNSIGNED)

            $table->primary(['user_id', 'customer_id']);

            // 1:1 (cada user mapea a un único customer y viceversa)
            $table->unique('user_id');
            $table->unique('customer_id');

            // FK a users
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            // FK a sakila.customer
            // Si tu app y Sakila están en la MISMA BD, basta con "customer".
            // Si estuvieran en esquemas distintos, usa ->on('sakila.customer') según tu conexión.
            $table->foreign('customer_id')
                ->references('customer_id')->on('customer')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_user');
    }
};
