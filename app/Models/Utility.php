<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Translations\TranslationTrait;
use DB;
use Laravel\Scout\Searchable;
class Utility extends Model
{
    use HasFactory, Sluggable, TranslationTrait, Searchable;
    protected $table = 'utilities';
    protected $fillable = [
        'name', 
        'description', 
        'image_id', 
        'type', 
        'status',
        'user_id', 
        'created_at',
        'updated_at',
    ];

    public function translation()
    {
        return $this->hasOne('App\Models\TranslationUtility', 'room_id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function translationCurrentLanguage()
    {
        return $this->hasOne('App\Models\TranslationUtility')->where('locale', app()->getLocale());
    }

    public function image()
    {
        return $this->belongsTo('App\Models\Image', 'image_id');
    }

    public function scopeFindOrFailWithAll($query, $id) {
        return $query->where('id', $id)->with('image', 'translationCurrentLanguage')->first();
    }
}
