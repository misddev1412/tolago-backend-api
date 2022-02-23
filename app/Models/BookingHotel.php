<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingHotel extends Model
{
    use HasFactory;
    protected $table = 'booking_hotels';
    //fields for booking_hotels
    protected $fillable = [
        'hotel_id',
        'room_id',
        'user_id',
        'email',
        'phone',
        'first_name',
        'last_name',
        'check_in',
        'check_out',
        'adults',
        'children',
        'infants',
        'notes',
        'total_price',
        'currency_id',
        'status',
        'transaction_id',
        'created_at',
        'updated_at',
    ];
    
}
