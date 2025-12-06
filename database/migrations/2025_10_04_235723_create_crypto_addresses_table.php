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
        Schema::create('crypto_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('network')->default('tron'); // tron, ethereum, bsc, polygon
            $table->string('token')->default('USDT');
            $table->string('address')->unique();
            $table->string('public_key')->nullable();
            $table->timestamp('address_requested_at')->nullable(); // Когда адрес был запрошен/обновлен
            $table->timestamps();

            $table->index(['user_id', 'network', 'token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_addresses');
    }
};
