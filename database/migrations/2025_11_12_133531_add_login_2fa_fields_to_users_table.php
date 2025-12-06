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
        Schema::table('users', function (Blueprint $table) {
            $table->string('login_2fa_code', 6)->nullable()->after('email_verification_code_expires_at');
            $table->timestamp('login_2fa_code_expires_at')->nullable()->after('login_2fa_code');
            $table->boolean('login_2fa_verified')->default(false)->after('login_2fa_code_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['login_2fa_code', 'login_2fa_code_expires_at', 'login_2fa_verified']);
        });
    }
};
