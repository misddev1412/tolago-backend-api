<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationHotel extends Model
{
    use HasFactory;
    protected $table = 'translation_hotels';
    protected $fillable = [
        'hotel_id', 
        'locale', 
        'name', 
        'slug', 
        'address', 
        'phone', 
        'email', 
        'website', 
        'description', 
        'meta_title',
        'meta_description',
        'meta_keywords',
        'created_at',
        'updated_at',
    ];
}
