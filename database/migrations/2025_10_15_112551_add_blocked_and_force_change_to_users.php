<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->timestamp('blocked_at')->nullable();
            $t->boolean('must_change_password')->default(false);
        });
    }
    
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['blocked_at','must_change_password']);
        });
    }
};
