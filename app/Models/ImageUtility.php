<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageUtility extends Model
{
    use HasFactory;
    protected $table = 'image_utilities';
    protected $fillable = ['utility_id', 'image_id'];

}
