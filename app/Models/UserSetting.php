<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;
    protected $table = 'user_setting';
    protected $fillable = [
        'user_id',
        'is_notification_on',
        'is_email_on',
        'is_sms_on',
        'is_push_on',
        'is_dark_mode',
        'display_chat_type',
        'default_timezone',
        'default_language',
        'default_currency',
        'datetime_format',
        'date_format',
        'time_format',
        'receive_friend_request',
        'receive_message',
        'receive_group_invite',
        'show_email',
        'show_phone',
        'show_address',
        'show_birthday',
        'show_social_links',
        'album_privacy',
        'post_privacy',
        'video_privacy',
        'photo_privacy',
        'search_by_email',
        'search_by_phone',
        'can_find_me',
        'can_access_closed_profile',
        'friend_privacy'

    ];

    //relation belongs to User
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
