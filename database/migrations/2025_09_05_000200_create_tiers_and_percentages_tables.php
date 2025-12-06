<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('level')->unique();
            $table->decimal('min_balance', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tier_percentages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tier_id')->constrained('tiers')->cascadeOnDelete();
            $table->unsignedInteger('days');
            $table->decimal('percentage', 5, 2);
            $table->timestamps();
            $table->unique(['tier_id', 'days']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_percentages');
        Schema::dropIfExists('tiers');
    }
};
