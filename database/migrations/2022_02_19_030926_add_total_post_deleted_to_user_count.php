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
            $table->bigInteger('total_post_deleted')->default(0)->after('total_posts');
            $table->bigInteger('total_post_deleted_by_me')->default(0)->after('total_post_deleted');
            $table->bigInteger('total_post_deleted_by_admin')->default(0)->after('total_post_deleted_by_me');
            $table->bigInteger('total_post_uploaded')->default(0)->after('total_post_deleted_by_admin');
            $table->bigInteger('total_post_published')->default(0)->after('total_post_uploaded');
            
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
            $table->dropColumn('total_post_deleted');
            $table->dropColumn('total_post_deleted_by_me');
            $table->dropColumn('total_post_deleted_by_admin');
            $table->dropColumn('total_post_uploaded');
            $table->dropColumn('total_post_published');
        });
    }
};
