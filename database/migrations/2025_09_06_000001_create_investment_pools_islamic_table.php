<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_pools_islamic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tier_id');
            $table->integer('duration_days');
            $table->decimal('min_percentage', 8, 4);
            $table->decimal('max_percentage', 8, 4);
            $table->timestamps();

            $table->foreign('tier_id')->references('id')->on('tiers')->onDelete('cascade');
            $table->unique(['tier_id', 'duration_days']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_pools_islamic');
    }
};
