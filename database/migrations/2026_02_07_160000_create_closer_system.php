<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_closer')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_closer')->default(false)->after('is_super_admin');
            });
        }

        Schema::create('closer_user_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('closer_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment')->nullable();
            $table->string('status')->nullable(); // int, no-int, re-call, fake
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('closer_user_notes');

        if (Schema::hasColumn('users', 'is_closer')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_closer');
            });
        }
    }
};
