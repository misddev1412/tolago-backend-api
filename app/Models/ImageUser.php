<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageUser extends Model
{
    use HasFactory;
    protected $table = 'image_users';
    protected $fillable = ['user_id', 'image_id'];
}
