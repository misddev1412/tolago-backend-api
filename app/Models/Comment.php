<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    

    
}
