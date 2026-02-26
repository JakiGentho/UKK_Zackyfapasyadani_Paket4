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
        Schema::table('product_carts', function (Blueprint $table) {
            $table->string('receiver_address')->nullable();
            $table->string('receiver_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_carts', function (Blueprint $table) {
            $table->dropColumn(['receiver_address', 'receiver_phone']);
        });
    }
};
