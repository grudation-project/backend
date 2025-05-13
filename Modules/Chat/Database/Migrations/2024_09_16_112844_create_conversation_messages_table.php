<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedTinyInteger('type');
            $table->longText('content')->nullable();
            $table->foreignUuid('conversation_member_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('conversation_id')->constrained()->cascadeOnDelete();
            $table->boolean('delivered')->default(false);
            $table->boolean('seen')->default(false);
            $table->unsignedBigInteger('record_duration')->nullable();
            $table->boolean('is_updated')->default(false);
            $table->geometry('location')->nullable(); // Create with geometry type initially
            $table->timestamps();
        });

        // Modify the column type to POINT, set NOT NULL, and set default value
        // DB::statement('ALTER TABLE `conversation_messages` MODIFY `location` POINT NOT NULL DEFAULT ST_GeomFromText(\'POINT(0 0)\')');
        //
        // // Add spatial index
        // DB::statement('ALTER TABLE `conversation_messages` ADD SPATIAL INDEX `location_index` (`location`)');
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_messages');
    }
};
