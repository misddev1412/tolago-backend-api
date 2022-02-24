<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Translations\TranslationTrait;
use DB;
use Laravel\Scout\Searchable;

class Room extends Model
{
    use HasFactory, Sluggable, TranslationTrait, Searchable;
    protected $table = 'rooms';
    protected $fillable = [
        'name', 
        'description', 
        'image_id', 
        'status',
        'maxium_guest',
        'maxium_child',
        'square_feet',
        'bed_type',
        'bed_quantity',
        'bed_quantity_extra',
        'view_type',
        'hotel_id',
        'user_id',
        'price',
        'currency_id',
        'created_at',
        'updated_at',
    ];

    public function hotel()
    {
        return $this->belongsTo('App\Models\Hotel');
    }

    public function roomPrice()
    {
        return $this->hasMany('App\Models\RoomPrice');
    }

    public function translation()
    {
        return $this->hasOne('App\Models\TranslationRoom', 'room_id');
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
        return $this->hasOne('App\Models\TranslationRoom')->where('locale', app()->getLocale());
    }

    public function image()
    {
        return $this->belongsTo('App\Models\Image', 'image_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_id');
    }

    public function images() // belongsToMany
    {
        return $this->belongsToMany('App\Models\Image', 'image_room', 'room_id', 'image_id');
    }

    public function scopeFindOrFailWithAll($query, $id) {
        return $query->where('id', $id)->with('hotel', 'roomPrice', 'image', 'images', 'translationCurrentLanguage')->first();
    }
}
