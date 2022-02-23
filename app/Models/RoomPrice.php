<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPrice extends Model
{
    use HasFactory;
    protected $table = 'room_prices';
    protected $fillable = [
        'room_id', 
        'price',
        'currency_id',
        'start_date', 
        'end_date', 
        'created_at',
        'updated_at',
    ];

    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }
}
