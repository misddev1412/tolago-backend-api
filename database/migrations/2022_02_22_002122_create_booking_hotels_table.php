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
        Schema::create('booking_hotels', function (Blueprint $table) {
            $table->id();
            //fields from booking_hotels model
            $table->unsignedBigInteger('hotel_id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('user_id');
            $table->string('email');
            $table->string('phone');
            $table->string('first_name');
            $table->string('last_name');
            $table->datetime('check_in');
            $table->datetime('check_out');
            $table->unsignedBigInteger('adults');
            $table->unsignedBigInteger('children');
            $table->unsignedBigInteger('infants');
            $table->text('notes');
            $table->float('total_price');
            $table->unsignedBigInteger('currency_id');
            $table->string('status');
            $table->string('transaction_id');
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
        Schema::dropIfExists('booking_hotels');
    }
};
