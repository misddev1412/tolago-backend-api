<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    //fields for likes model with user_id and likeable_id
    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
        'status',
        'created_at',
        'updated_at',
    ];

    //polymorph model
    public function likeable()
    {
        return $this->morphTo();
    }
}
