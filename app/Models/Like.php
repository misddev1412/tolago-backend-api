<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    //fields for likes model with user_id and likeable_id
    const LIKE_TYPE_LIKE = 1;
    const LIKE_TYPE_HAHA = 2;

    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
        'status',
        'like_type_id',
        'created_at',
        'updated_at',
    ];

    //polymorph model
    public function likeable()
    {
        return $this->morphTo();
    }

    public function likeType()
    {
        return $this->belongsTo('App\Models\LikeType', 'like_type_id');
    }
}
