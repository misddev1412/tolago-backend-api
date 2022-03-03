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
        'orignal_url',
        'thumbnail_url',
        '240p_url',
        '360p_url',
        '480p_url',
        '720p_url',
        '1080p_url',
        '2048p_url',
        '4096p_url',
        'hls_url',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
