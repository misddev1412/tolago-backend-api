<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $table = 'rooms';
    protected $fillable = [
        'name', 
        'description', 
        'image_id', 
        'status',
        'maxium_guest',
        'maxium_child',
        'square_feet',
        'bed_type',
        'bed_quantity',
        'bed_quantity_extra',
        'view_type',
        'hotel_id',
        'price',
        'currency_id',
        'created_at',
        'updated_at',
    ];

    public function hotel()
    {
        return $this->belongsTo('App\Models\Hotel');
    }

    public function roomPrice()
    {
        return $this->hasMany('App\Models\RoomPrice');
    }
}
