<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageVideo extends Model
{
    use HasFactory;
    //tables
    protected $table = 'image_videos';
    //fields image_id and video_id
    protected $fillable = ['image_id', 'video_id'];


}
