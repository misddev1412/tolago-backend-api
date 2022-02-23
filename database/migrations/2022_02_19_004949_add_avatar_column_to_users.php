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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->nullable()->after('password');
            $table->string('status')->default('active')->nullable()->after('image_id');
            $table->string('slug')->nullable()->after('status');
            $table->string('phone_number')->nullable()->after('slug');
            $table->tinyInteger('is_phone_verified')->default(0)->nullable()->after('phone_number');
            $table->string('facebook_id')->nullable()->after('phone_number');
            $table->string('google_id')->nullable()->after('facebook_id');
            $table->string('twitter_id')->nullable()->after('google_id');
            $table->string('linkedin_id')->nullable()->after('twitter_id');
            $table->string('qr_code')->nullable()->after('linkedin_id');
            $table->string('cover_image')->nullable()->after('qr_code');
            $table->string('username')->nullable()->after('cover_image');
            $table->string('display_name')->nullable()->after('username');
            $table->date('birthday')->nullable()->after('display_name');
            $table->string('gender')->nullable()->after('birthday');
            $table->string('address_book_id')->nullable()->after('gender');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('image_id');
            $table->dropColumn('status');
            $table->dropColumn('slug');
            $table->dropColumn('phone_number');
            $table->dropColumn('is_phone_verified');
            $table->dropColumn('facebook_id');
            $table->dropColumn('google_id');
            $table->dropColumn('twitter_id');
            $table->dropColumn('linkedin_id');
            $table->dropColumn('qr_code');
            $table->dropColumn('cover_image');
            $table->dropColumn('username');
            $table->dropColumn('display_name');
            $table->dropColumn('birthday');
            $table->dropColumn('gender');
            $table->dropColumn('address_book_id');
        });
    }
};
