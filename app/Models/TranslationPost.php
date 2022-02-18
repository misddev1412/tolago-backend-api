<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationPost extends Model
{
    use HasFactory;
    //fields for translation_posts model
    protected $fillable = [
        'post_id',
        'title',
        'body',
        'locale',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'created_at',
        'updated_at',
    ];

    //relationship with posts model
    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }

    //scope for locale
    public function scopeLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    
}
