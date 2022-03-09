<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $table = 'videos';
    //fields for videos
    protected $fillable = [
        'original_url',
        '240p_url',
        '360p_url',
        '480p_url',
        '720p_url',
        '1080p_url',
        '2048p_url',
        '4096p_url',
        'hls_url',
        'user_id',
        'duration_in_seconds',
        'created_at',
        'updated_at',
    ];

    public function getOriginalUrlAttribute()
    {
        return env('UPLOAD_ASSET_PATH') . '/' . $this->attributes['original_url'];
    }


    public function getDurationInSecondsAttribute()
    {
        return  gmdate("H:i:s", $this->attributes['duration_in_seconds']);
    }

    //relationships with images table and videos table
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function images()
    {
        return $this->belongsToMany('App\Models\Image', 'image_videos', 'video_id', 'image_id');
    }
}
