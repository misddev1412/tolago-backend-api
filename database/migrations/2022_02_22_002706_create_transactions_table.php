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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            //fields for transaction
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('to_user_id');
            $table->float('amount');
            $table->unsignedBigInteger('currency_id');
            $table->float('rate');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->string('payment_method_detail');
            $table->string('delivery_method');
            $table->string('type');
            $table->string('status');
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
        Schema::dropIfExists('transactions');
    }
};
