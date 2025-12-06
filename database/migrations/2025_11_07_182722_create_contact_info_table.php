<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_info', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'main', 'support', etc.
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('telegram')->nullable();
            $table->timestamps();
        });

        // Insert default record
        DB::table('contact_info')->insert([
            'key' => 'main',
            'email' => 'support@lumastake.com',
            'phone' => '+1234567890',
            'address' => '123 Main St, City, Country',
            'telegram' => '@lumastake_support',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_info');
    }
};
