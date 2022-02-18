<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCount extends Model
{
    use HasFactory;
    protected $table = 'user_count';
    protected $fillable = [
        'user_id',
        'total_followers',
        'total_following',
        'total_posts',
        'total_likes',
        'total_comments',
        'total_views',
        'total_shares',
        'total_friends',
        'total_reports',
        'total_coins',
        'total_points',
        'total_groups_joined',
        'total_groups_created',
        'total_images_in_albums',
        'total_albums',
        'total_videos_uploaded',
        'total_notifications_received',
        'total_messages_received',
        'total_messages_sent',
        'total_messages_deleted',
        'total_address_book',
        'total_friends_pending',
        'total_friends_requested',
        'total_friends_accepted',
        'total_friends_declined',
        'total_friends_blocked',
        'total_friends_unblocked',
        'total_friends_deleted',
        'total_comments_replied',
        'total_comments_liked',
        'total_comments_deleted',
        'total_products_sold',
        'total_products_bought',
        'total_products_liked',
        'total_products_disliked',
        'total_products_viewed',
        'total_products_shared',
        'total_products_commented',
        'total_products_reported',
        'total_orders_pending',
        'total_orders_processing',
        'total_orders_shipped',
        'total_orders_delivered',
        'total_orders_cancelled',
        'total_orders_refunded',
        'total_products_uploaded',
        'total_post_deleted',
        'total_post_deleted_by_me',
        'total_post_deleted_by_admin',
        'total_post_uploaded',
        'total_post_published',
        'total_image_uploaded',
        
    ];

    //relation belongs to User
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
