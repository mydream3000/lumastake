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
        // Set all existing users to active
        DB::table('users')->update(['active' => true]);

        // Change column default to true (already done in main migration)
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('active')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('active')->default(false)->change();
        });
    }
};
