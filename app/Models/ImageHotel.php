<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageHotel extends Model
{
    use HasFactory;
    protected $table = 'image_hotel';
    protected $fillable = ['hotel_id', 'image_id'];
}
