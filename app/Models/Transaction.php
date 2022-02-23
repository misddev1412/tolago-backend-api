<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    //fields for transaction
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'amount',
        'currency_id',
        'rate',
        'model_type',
        'model_id',
        'payment_method_id',
        'payment_method_detail',
        'delivery_method',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];

    public function fromUser()
    {
        return $this->belongsTo('App\Models\User', 'from_user_id') ?? null;
    }

    public function toUser()
    {
        return $this->belongsTo('App\Models\User', 'to_user_id') ?? null;
    }

    public function model()
    {
        return $this->morphTo('model_type', 'model_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'payment_method_id') ?? null;
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_id') ?? null;
    }
    
}
