<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['deposit', 'withdraw', 'staking', 'reward', 'profit', 'referral_reward', 'promo']);
            $table->enum('status', ['pending', 'confirmed', 'failed', 'cancelled'])->default('pending');
            $table->decimal('amount', 18, 2);
            $table->string('description')->nullable();
            $table->string('tx_hash')->nullable();
            $table->json('meta')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['type']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
