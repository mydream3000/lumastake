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
        Schema::create('referral_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('level')->unique(); // 1, 2, 3, 4, 5
            $table->string('name'); // "1–5 партнёров", "6–10 партнёров" и т.д.
            $table->unsignedInteger('min_partners'); // Минимальное количество активных рефералов для этого уровня
            $table->decimal('reward_percentage', 5, 2); // Процент награды (10.00, 12.00 и т.д.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_levels');
    }
};
