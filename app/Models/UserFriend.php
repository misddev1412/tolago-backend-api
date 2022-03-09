<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Laravel\Scout\Searchable;

class UserFriend extends Model
{
    public const PENDING = 'pending';
    public const ACCEPTED = 'accepted';
    use HasFactory, Searchable;
    //fields for user_friends table
    protected $fillable = ['user_id', 'friend_id', 'status', 'created_at', 'updated_at'];

    //user relation
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //friend relation
    public function friend() {
        return $this->belongsTo(User::class);
    }

    //scope status accepted
    public function scopeAccepted($query) {
        return $query->where('status', 'accepted');
    }

    public function toSearchableArray()
    {
        $array = $this->only('user_id', 'friend_id');
        $array['created_at'] = $this->created_at->timestamp;
        $array['user'] = $this->user;
        $array['friend'] = $this->friend;

        return $array;
    }


}
