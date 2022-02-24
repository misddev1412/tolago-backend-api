<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    use HasFactory;
    protected $table = 'utilities';
    protected $fillable = [
        'name', 
        'description', 
        'image_id', 
        'type', 
        'status',
        'user_id', 
        'created_at',
        'updated_at',
    ];
}
