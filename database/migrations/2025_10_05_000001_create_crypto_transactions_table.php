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
        Schema::create('crypto_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('network')->default('tron'); // tron, ethereum, bsc, polygon
            $table->string('token')->default('USDT');
            $table->string('address'); // Адрес получателя
            $table->string('tx_hash')->unique(); // Hash транзакции
            $table->decimal('amount', 18, 8);
            $table->unsignedInteger('confirmations')->default(0);
            $table->boolean('processed')->default(false); // Обработана ли транзакция
            $table->json('ipn_data')->nullable(); // Данные от IPN для отладки
            $table->timestamps();

            $table->index(['user_id', 'processed']);
            $table->index('tx_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_transactions');
    }
};
