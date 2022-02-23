<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $table = 'currencies';
    //fields for currency
    protected $fillable = [
        'name', 
        'code', 
        'symbol', 
        'rate_to_usd', 
        'is_active', 
        'is_default', 
        'created_at', 
        'updated_at',
    ];
}
