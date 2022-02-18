<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageMessage extends Model
{
    use HasFactory;
    protected $table = 'image_message';
    //fields for images
    protected $fillable = [
        'message_id',
        'image_id',
    ];


    
}
