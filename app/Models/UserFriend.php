<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserFriend extends Model
{
    public const PENDING = 'pending';
    public const ACCEPTED = 'accepted';
    use HasFactory;
    //fields for user_friends table
    protected $fillable = ['user_id', 'friend_id', 'status', 'created_at', 'updated_at'];

    //user relation
    public function user() {
        return $this->belongsTo(User::class);
    }

    //friend relation
    public function friend() {
        return $this->belongsTo(User::class);
    }

    //scope status accepted
    public function scopeAccepted($query) {
        return $query->where('status', 'accepted');
    }
    
}
