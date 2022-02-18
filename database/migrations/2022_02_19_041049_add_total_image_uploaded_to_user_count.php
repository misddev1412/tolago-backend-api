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
        Schema::table('user_count', function (Blueprint $table) {
            $table->bigInteger('total_image_uploaded')->default(0)->after('total_images_in_albums');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_count', function (Blueprint $table) {
            //
            $table->dropColumn('total_image_uploaded');
        });
    }
};
