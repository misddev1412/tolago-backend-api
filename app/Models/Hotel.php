<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Translations\TranslationTrait;
use DB;
use Laravel\Scout\Searchable;
    
class Hotel extends Model
{
    use HasFactory, Sluggable, TranslationTrait, Searchable;
    protected $table = 'hotels';
    protected $fillable = [
        'name', 
        'slug',
        'address', 
        'phone', 
        'email', 
        'website', 
        'description', 
        'image_id', 
        'lat', 
        'lng', 
        'user_id',
        'status',
        'is_featured',
        'check_in',
        'check_out',
        'country_id',
        'state_id',
        'city_id',
        'created_at',
        'updated_at',
    ];

    public function translation()
    {
        return $this->hasOne('App\Models\TranslationHotel', 'hotel_id');
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
        return $this->hasOne('App\Models\TranslationHotel')->where('locale', app()->getLocale());
    }

    public function image()
    {
        return $this->belongsTo('App\Models\Image', 'image_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function state() // belongsTo
    {
        return $this->belongsTo('App\Models\State', 'state_id');
    }

    public function city() // belongsTo
    {
        return $this->belongsTo('App\Models\City', 'city_id');
    }
    

    public function images() //belongstomany
    {
        return $this->belongsToMany('App\Models\Image', 'image_hotel', 'hotel_id', 'image_id');
    }

    public function rooms()
    {
        return $this->hasMany('App\Models\Room', 'hotel_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeFindOrFailWithAll($query, $id) {
        return $query->where('id', $id)->with('user', 'country', 'state', 'city', 'images', 'translationCurrentLanguage')->first();
    }
}
