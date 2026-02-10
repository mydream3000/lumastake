<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'account_type_changed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('account_type_changed_at')->nullable()->after('account_type');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_type_changed_at');
        });
    }
};
