<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'blocked')) {
                $table->boolean('blocked')->default(false)->after('referral_level_id');
                $table->index('blocked');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'blocked')) {
                $table->dropIndex(['blocked']);
                $table->dropColumn('blocked');
            }
        });
    }
};
