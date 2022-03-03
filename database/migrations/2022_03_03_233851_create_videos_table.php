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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            //fields from Video model
            $table->string('original_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('240p_url')->nullable();
            $table->string('360p_url')->nullable();
            $table->string('480p_url')->nullable();
            $table->string('720p_url')->nullable();
            $table->string('1080p_url')->nullable();
            $table->string('2048p_url')->nullable();
            $table->string('4096p_url')->nullable();
            $table->string('hls_url')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
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
        Schema::dropIfExists('videos');
    }
};
