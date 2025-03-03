<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verify_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('handle');
            $table->string('code'); // SHA 256
            $table->timestamp('expires_at');
            $table->unsignedTinyInteger('type'); // from VerifyTokenTypeEnum
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verify_tokens');
    }
};
