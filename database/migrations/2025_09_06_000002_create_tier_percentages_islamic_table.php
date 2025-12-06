<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tier_percentages_islamic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tier_id');
            $table->integer('duration_days');
            $table->string('min_percentage');
            $table->string('max_percentage');
            $table->timestamps();

            $table->foreign('tier_id')->references('id')->on('tiers')->onDelete('cascade');
            $table->unique(['tier_id', 'duration_days']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_percentages_islamic');
    }
};
