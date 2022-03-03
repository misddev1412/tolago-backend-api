<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    //fields for images
    protected $fillable = [
        'default',
        'thumbnail',
        'medium',
        'large',
        'original',
        'user_id',
    ];

    //relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDefaultAttribute()
    {
        return env('UPLOAD_ASSET_PATH') . '/' . $this->attributes['default'];
    }

    public function getThumbnailAttribute()
    {
        return env('UPLOAD_ASSET_PATH') . '/' . $this->attributes['thumbnail'];
    }

    public function getMediumAttribute()
    {
        return env('UPLOAD_ASSET_PATH') . '/' . $this->attributes['medium'];
    }

    public function getLargeAttribute()
    {
        return env('UPLOAD_ASSET_PATH') . '/' . $this->attributes['large'];
    }

    public function getOriginalAttribute()
    {
        return env('UPLOAD_ASSET_PATH') . '/' . $this->attributes['original'];
    }    

}
