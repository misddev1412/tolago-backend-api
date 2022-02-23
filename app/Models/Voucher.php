<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';

    public const MAX_USES_TYPE_UNLIMITED = 'unlimited';
    public const MAX_USES_TYPE_DAILY = 'daily';
    public const MAX_USES_TYPE_WEEKLY = 'weekly';
    public const MAX_USES_TYPE_MONTHLY = 'monthly';
    public const MAX_USES_TYPE_YEARLY = 'yearly';

    use HasFactory;
    protected $table = 'vouchers';
    protected $fillable = [
        'user_id', 
        'cpde', 
        'type', 
        'value', 
        'status', 
        'table_name',
        'table_id',
        'minimum_order',
        'maximum_discount',
        'max_uses',
        'used',
        'max_uses_type',
        'expired_at',  
        'reated_at', 
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
