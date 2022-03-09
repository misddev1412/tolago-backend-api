<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Comment extends Model
{
    use HasFactory;
    //fields for comments model with user_id and commentable_id
    protected $fillable = [
        'user_id',
        'commentable_id',
        'commentable_type',
        'comment',
        'status',
        'created_at',
        'updated_at',
    ];
    protected $appends = ['like_types', 'my_like', 'total_likes'];

    public function getLikeTypesAttribute()
    {
        return LikeType::get();
    }

    public function getMyLikeAttribute()
    {
        $user = auth('api')->user();
        if ($user) {
            $like = $this->myLike($user->id);
            return $like;
        }
        return null;
    }

    //relationship with user model
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    //morph model
    public function commentable()
    {
        return $this->morphTo();
    }

    //relationship with like polymorphic
    public function likes()
    {
        return $this->morphMany('App\Models\Like', 'likeable');
    }

    public function myLike($user_id) {
        return $this->likes()->with('likeType')->where('user_id', $user_id)->first();
    }

    public static function countGroupLikeType($commentId) {
        return DB::select("SELECT like_type_id, CONCAT(?, icon) as icon, count(*) as count FROM likes JOIN like_types ON likes.like_type_id = like_types.id WHERE likeable_id = ? AND likeable_type = ? GROUP BY like_type_id", [env('UPLOAD_ASSET_PATH') . '/', $commentId, 'App\Models\Comment']);
    }

    public function getTotalLikesAttribute() {
        return $this->countGroupLikeType($this->id);
    }




}
