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
            $table->bigInteger('total_followers')->nullable()->default(0)->after('user_id');
            $table->bigInteger('total_following')->nullable()->default(0)->after('total_followers');
            $table->bigInteger('total_posts')->default(0)->nullable()->after('total_following');
            $table->bigInteger('total_products_sold')->default(0)->nullable()->after('total_posts');
            $table->bigInteger('total_products_uploaded')->default(0)->nullable()->after('total_products_sold');
            $table->bigInteger('total_products_bought')->default(0)->nullable()->after('total_products_sold');
            $table->bigInteger('total_products_liked')->default(0)->nullable()->after('total_products_bought');
            $table->bigInteger('total_products_disliked')->default(0)->nullable()->after('total_products_liked');
            $table->bigInteger('total_products_viewed')->default(0)->nullable()->after('total_products_disliked');
            $table->bigInteger('total_products_shared')->default(0)->nullable()->after('total_products_viewed');
            $table->bigInteger('total_products_commented')->default(0)->nullable()->after('total_products_shared');
            $table->bigInteger('total_products_reported')->default(0)->nullable()->after('total_products_commented');
            $table->bigInteger('total_orders_pending')->default(0)->nullable()->after('total_products_reported');
            $table->bigInteger('total_orders_processing')->default(0)->nullable()->after('total_orders_pending');
            $table->bigInteger('total_orders_shipped')->default(0)->nullable()->after('total_orders_processing');
            $table->bigInteger('total_orders_delivered')->default(0)->nullable()->after('total_orders_shipped');
            $table->bigInteger('total_orders_cancelled')->default(0)->nullable()->after('total_orders_delivered');
            $table->bigInteger('total_orders_refunded')->default(0)->nullable()->after('total_orders_cancelled');
            $table->bigInteger('total_likes')->default(0)->nullable()->after('total_posts');
            $table->bigInteger('total_comments')->default(0)->nullable()->after('total_likes');
            $table->bigInteger('total_comments_replied')->default(0)->nullable()->after('total_comments');
            $table->bigInteger('total_comments_liked')->default(0)->nullable()->after('total_comments_replied');
            $table->bigInteger('total_comments_deleted')->default(0)->nullable()->after('total_comments_liked');
            $table->bigInteger('total_views')->default(0)->nullable()->after('total_comments');
            $table->bigInteger('total_shares')->default(0)->nullable()->after('total_views');
            $table->bigInteger('total_friends')->default(0)->nullable()->after('total_shares');
            $table->bigInteger('total_friends_pending')->default(0)->nullable()->after('total_friends');
            $table->bigInteger('total_friends_requested')->default(0)->nullable()->after('total_friends_pending');
            $table->bigInteger('total_friends_accepted')->default(0)->nullable()->after('total_friends_requested');
            $table->bigInteger('total_friends_declined')->default(0)->nullable()->after('total_friends_accepted');
            $table->bigInteger('total_friends_blocked')->default(0)->nullable()->after('total_friends_declined');
            $table->bigInteger('total_friends_unblocked')->default(0)->nullable()->after('total_friends_blocked');
            $table->bigInteger('total_friends_deleted')->default(0)->nullable()->after('total_friends_unblocked');
            $table->bigInteger('total_reports')->default(0)->nullable()->after('total_friends');
            $table->bigInteger('total_coins')->default(0)->nullable()->after('total_reports');
            $table->bigInteger('total_points')->default(0)->nullable()->after('total_coins');
            $table->bigInteger('total_groups_joined')->default(0)->nullable()->after('total_points');
            $table->bigInteger('total_groups_created')->default(0)->nullable()->after('total_groups_joined');
            $table->bigInteger('total_images_in_albums')->default(0)->nullable()->after('total_groups_created');
            $table->bigInteger('total_albums')->default(0)->nullable()->after('total_images_in_albums');
            $table->bigInteger('total_videos_uploaded')->default(0)->nullable()->after('total_albums');
            $table->bigInteger('total_notifications_received')->default(0)->nullable()->after('total_videos_uploaded');
            $table->bigInteger('total_messages_received')->default(0)->nullable()->after('total_notifications_received');
            $table->bigInteger('total_messages_sent')->default(0)->nullable()->after('total_messages_received');
            $table->bigInteger('total_messages_deleted')->default(0)->nullable()->after('total_messages_sent');
            $table->bigInteger('total_address_book')->default(0)->nullable()->after('total_messages_deleted');
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
            $table->dropColumn('total_followers');
            $table->dropColumn('total_following');
            $table->dropColumn('total_posts');
            $table->dropColumn('total_products_sold');
            $table->dropColumn('total_products_uploaded');
            $table->dropColumn('total_products_bought');
            $table->dropColumn('total_products_liked');
            $table->dropColumn('total_products_disliked');
            $table->dropColumn('total_products_viewed');
            $table->dropColumn('total_products_shared');
            $table->dropColumn('total_products_commented');
            $table->dropColumn('total_products_reported');
            $table->dropColumn('total_orders_pending');
            $table->dropColumn('total_orders_processing');
            $table->dropColumn('total_orders_shipped');
            $table->dropColumn('total_orders_delivered');
            $table->dropColumn('total_orders_cancelled');
            $table->dropColumn('total_orders_refunded');
            $table->dropColumn('total_likes');
            $table->dropColumn('total_comments');
            $table->dropColumn('total_comments_replied');
            $table->dropColumn('total_comments_liked');
            $table->dropColumn('total_comments_deleted');
            $table->dropColumn('total_views');
            $table->dropColumn('total_shares');
            $table->dropColumn('total_friends');
            $table->dropColumn('total_friends_pending');
            $table->dropColumn('total_friends_requested');
            $table->dropColumn('total_friends_accepted');
            $table->dropColumn('total_friends_declined');
            $table->dropColumn('total_friends_blocked');
            $table->dropColumn('total_friends_unblocked');
            $table->dropColumn('total_friends_deleted');
            $table->dropColumn('total_reports');
            $table->dropColumn('total_coins');
            $table->dropColumn('total_points');
            $table->dropColumn('total_groups_joined');
            $table->dropColumn('total_groups_created');
            $table->dropColumn('total_images_in_albums');
            $table->dropColumn('total_albums');
            $table->dropColumn('total_videos_uploaded');
            $table->dropColumn('total_notifications_received');
            $table->dropColumn('total_messages_received');
            $table->dropColumn('total_messages_sent');
            $table->dropColumn('total_messages_deleted');
            $table->dropColumn('total_address_book');
        });
    }
};
