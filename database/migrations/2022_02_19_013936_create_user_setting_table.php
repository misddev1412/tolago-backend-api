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
        Schema::create('user_setting', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('is_notification_on')->default(1);
            $table->tinyInteger('is_email_on')->default(1);
            $table->tinyInteger('is_sms_on')->default(1);
            $table->tinyInteger('is_push_on')->default(1);
            $table->tinyInteger('is_dark_mode')->default(0);
            $table->integer('display_chat_type')->default(1);
            $table->string('default_timezone')->default('UTC');
            $table->string('default_language')->default('en');
            $table->string('default_currency')->default('USD');
            $table->string('datetime_format')->default('YYYY-MM-DD HH:mm:ss');
            $table->string('date_format')->default('YYYY-MM-DD');
            $table->string('time_format')->default('HH:mm:ss');
            $table->tinyInteger('receive_friend_request')->default(1);
            $table->tinyInteger('receive_message')->default(1);
            $table->tinyInteger('receive_group_invite')->default(1);
            $table->tinyInteger('show_email')->default(1);
            $table->tinyInteger('show_phone')->default(1);
            $table->tinyInteger('show_address')->default(1);
            $table->tinyInteger('show_birthday')->default(1);
            $table->tinyInteger('show_social_links')->default(1);
            $table->string('album_privacy')->default('everyone');
            $table->string('post_privacy')->default('everyone');
            $table->string('video_privacy')->default('everyone');
            $table->string('photo_privacy')->default('everyone');
            $table->tinyInteger('search_by_email')->default(1);
            $table->tinyInteger('search_by_phone')->default(1);
            $table->tinyInteger('can_find_me')->default(1);
            $table->tinyInteger('can_access_closed_profile')->default(1);
            $table->string('friend_privacy')->default('everyone');
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
        Schema::dropIfExists('user_setting');
    }
};
