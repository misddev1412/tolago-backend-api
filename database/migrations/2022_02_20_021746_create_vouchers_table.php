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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            //get fields from Voucher Model
            $table->unsignedBigInteger('user_id');
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->string('value')->nullable();
            $table->string('status')->nullable();
            $table->string('table_name')->nullable();
            $table->string('table_id')->nullable();
            $table->bigInteger('minimum_order')->default(0);
            $table->bigInteger('maximum_discount')->default(0);

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
        Schema::dropIfExists('vouchers');
    }
};
