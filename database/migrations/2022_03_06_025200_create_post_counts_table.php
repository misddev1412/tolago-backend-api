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
        Schema::create('post_count', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            //get fields from PostCount model
            $table->bigInteger('total_views')->default(0);
            $table->bigInteger('total_likes')->default(0);
            $table->bigInteger('total_comments')->default(0);
            $table->bigInteger('total_shares')->default(0);
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
        Schema::dropIfExists('post_counts');
    }
};
