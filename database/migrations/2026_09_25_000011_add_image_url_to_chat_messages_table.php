<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('chat_messages', 'image_url')) {
                    $table->text('image_url')->nullable()->after('gift_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                if (Schema::hasColumn('chat_messages', 'image_url')) {
                    $table->dropColumn('image_url');
                }
            });
        }
    }
};
