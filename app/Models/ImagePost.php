<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagePost extends Model
{
    use HasFactory;
    protected $table = 'image_post';
    //fields for images
    protected $fillable = [
        'post_id',
        'image_id',
    ];


    
}
