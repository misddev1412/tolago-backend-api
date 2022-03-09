<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCount extends Model
{
    use HasFactory;
    //tables
    protected $table = 'post_count';
    //fields
    protected $fillable = ['post_id', 'total_views', 'total_likes', 'total_comments', 'total_shares'];

    //relationships
    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }
}
