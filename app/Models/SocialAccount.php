<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use HasFactory;
    protected $table = 'social_accounts';
    //fields
    protected $fillable = [
        'user_id',
        'provider_user_id',
        'provider',
    ];

    //relationships
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
