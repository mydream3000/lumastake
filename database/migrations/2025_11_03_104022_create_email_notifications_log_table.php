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
        Schema::create('email_notifications_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('template_key'); // e.g., 'staking_expiring_soon'
            $table->string('email'); // recipient email
            $table->string('subject');
            $table->string('related_type')->nullable(); // e.g., 'staking_deposit'
            $table->unsignedBigInteger('related_id')->nullable(); // e.g., staking deposit ID
            $table->timestamp('sent_at');
            $table->timestamps();

            // Index for quick lookups (short name to avoid MySQL 64 char limit)
            $table->index(['related_type', 'related_id', 'template_key'], 'email_notif_lookup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notifications_log');
    }
};
