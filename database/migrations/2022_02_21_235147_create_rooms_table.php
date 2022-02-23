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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            //fields from rooms model
            $table->string('name');
            $table->string('description');
            $table->unsignedBigInteger('image_id');
            $table->string('status');
            $table->unsignedBigInteger('maxium_guest');
            $table->unsignedBigInteger('maxium_child');
            $table->unsignedBigInteger('square_feet');
            $table->string('bed_type');
            $table->unsignedBigInteger('bed_quantity');
            $table->unsignedBigInteger('bed_quantity_extra');
            $table->string('view_type');
            $table->float('price');
            $table->unsignedBigInteger('currency_id');
            $table->unsignedBigInteger('hotel_id');
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
        Schema::dropIfExists('rooms');
    }
};
