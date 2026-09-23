<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('call_sessions')) {
            Schema::create('call_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('caller_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
                $table->string('channel_name');
                $table->integer('duration_seconds')->default(0);
                $table->integer('tokens_spent')->default(0);
                $table->integer('credits_earned')->default(0);
                $table->string('status')->default('ended'); // active, ended, missed
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('call_sessions');
    }
};
