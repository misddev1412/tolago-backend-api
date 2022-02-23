<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityRoom extends Model
{
    use HasFactory;
    protected $table = 'utility_rooms';
    protected $fillable = [
        'utility_id', 
        'room_id', 
        'created_at',
        'updated_at',
    ];
}
