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
        Schema::create('telegram_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('cascade');
            $table->foreignId('crypto_transaction_id')->nullable()->constrained('crypto_transactions')->onDelete('cascade');
            $table->enum('message_type', ['deposit_created', 'deposit_confirmed', 'withdraw_created', 'withdraw_confirmed', 'withdraw_rejected']);
            $table->text('message_text');
            $table->string('tx_hash')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_notifications');
    }
};
