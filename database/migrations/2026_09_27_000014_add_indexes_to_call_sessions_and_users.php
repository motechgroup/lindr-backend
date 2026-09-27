<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'last_heartbeat_at')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->index(['gender', 'last_heartbeat_at', 'is_admin'], 'users_online_gender_idx');
                });
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasTable('call_sessions')) {
                Schema::table('call_sessions', function (Blueprint $table) {
                    $table->index(['receiver_id', 'status', 'created_at'], 'calls_receiver_status_created_idx');
                    $table->index(['caller_id', 'status'], 'calls_caller_status_idx');
                });
            }
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        try {
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropIndex('users_online_gender_idx');
                });
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasTable('call_sessions')) {
                Schema::table('call_sessions', function (Blueprint $table) {
                    $table->dropIndex('calls_receiver_status_created_idx');
                    $table->dropIndex('calls_caller_status_idx');
                });
            }
        } catch (\Throwable $e) {}
    }
};
