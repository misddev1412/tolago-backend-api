<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityHotel extends Model
{
    use HasFactory;
    protected $table = 'utility_hotels';
    protected $fillable = [
        'utility_id', 
        'hotel_id', 
        'created_at',
        'updated_at',
    ];
}
