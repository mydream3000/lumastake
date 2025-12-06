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
        Schema::table('telegram_notifications', function (Blueprint $table) {
            $table->string('message_type', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telegram_notifications', function (Blueprint $table) {
            $table->enum('message_type', ['deposit_created', 'deposit_confirmed', 'withdraw_created', 'withdraw_confirmed', 'withdraw_rejected'])->change();
        });
    }
};
