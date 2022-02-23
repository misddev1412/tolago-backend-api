<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'payment_methods';
    //fields for payment_methods
    protected $fillable = [
        'name',
        'description',
        'client_id',
        'client_secret_key',
        'client_access_key',
        'image_id',
        'status',
        'callback_url',
        'order_url',
        'return_url',
        'created_at',
        'updated_at',
    ];
}
