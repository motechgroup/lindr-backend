<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->integer('reward_exp')->default(100);
            $table->integer('reward_credits')->default(50);
            $table->boolean('is_completed')->default(false);
            $table->string('progress_text')->nullable();
            $table->string('icon')->default('checkmark-circle');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tasks');
    }
};
