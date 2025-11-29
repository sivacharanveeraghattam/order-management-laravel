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
        Schema::table('users', fn($t) => $t->softDeletes());
        Schema::table('products', fn($t) => $t->softDeletes());
        Schema::table('orders', fn($t) => $t->softDeletes());
        Schema::table('order_items', fn($t) => $t->softDeletes());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
