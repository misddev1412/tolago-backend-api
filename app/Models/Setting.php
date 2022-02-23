<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $table = 'settings';
    //fields
    protected $fillable = [
        'key',
        'value',
        'description',
        'created_at',
        'updated_at'
    ];
}
