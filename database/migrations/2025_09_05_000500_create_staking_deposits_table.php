<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staking_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tier_id')->nullable()->constrained('tiers')->nullOnDelete();
            $table->decimal('amount', 18, 2);
            $table->unsignedInteger('days');
            $table->decimal('percentage', 5, 2);
            $table->decimal('earned_profit', 15, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->boolean('auto_renewal')->default(false);
            $table->timestamps();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staking_deposits');
    }
};
