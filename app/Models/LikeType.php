<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeType extends Model
{
    use HasFactory;
    //tables
    protected $table = 'like_types';
    //fields
    protected $fillable = ['name', 'icon', 'is_active'];


    public function getIconAttribute()
    {
        return env('UPLOAD_ASSET_PATH') . '/' . $this->attributes['icon'];

    }
}
