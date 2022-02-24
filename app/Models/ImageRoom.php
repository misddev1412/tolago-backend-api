<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageRoom extends Model
{
    use HasFactory;
    protected $table = 'image_room';
    protected $fillable = [
        'image_id', 
        'room_id', 
        'created_at',
        'updated_at',
    ];
}
