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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('tipe', 20)->default('dm');
            $table->unsignedBigInteger('user_a_id')->nullable();
            $table->unsignedBigInteger('user_b_id')->nullable();
            $table->timestamps();

            $table->foreign('user_a_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('user_b_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['user_a_id', 'user_b_id']);
            $table->index('tipe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
