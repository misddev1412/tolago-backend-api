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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('client_id');
            $table->string('client_secret_key');
            $table->string('client_access_key');
            $table->unsignedBigInteger('image_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('callback_url');
            $table->string('order_url');
            $table->string('return_url');
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
        Schema::dropIfExists('payment_methods');
    }
};
