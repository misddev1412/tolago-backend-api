<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';
    protected $fillable = ['name', 'iso3', 'numeric_code', 'iso2', 'phone_code', 'capital', 'currency', 'currency_name', 'created_at', 'updated_at'];
}
