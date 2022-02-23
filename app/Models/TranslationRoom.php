<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationRoom extends Model
{
    use HasFactory;
    protected $table = 'translation_rooms';
    protected $fillable = [
        'room_id', 
        'locale', 
        'name', 
        'description', 
        'meta_title',
        'meta_description',
        'meta_keywords',
        'created_at',
        'updated_at',
    ];
}
