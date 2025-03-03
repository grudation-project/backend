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
        Schema::create('conversation_message_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_message_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('conversation_member_id')->constrained()->cascadeOnDelete();
            $table->boolean('seen')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_message_actions');
    }
};
