<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationUtility extends Model
{
    use HasFactory;
    protected $table = 'translation_utilities';
    protected $fillable = [
        'utility_id', 
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
