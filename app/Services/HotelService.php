<?php

namespace App\Services;
use Cache;
use App\Models\Hotel;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class HotelService
{
    //variable hotel
    protected $hotel;

    public function __construct($hotel)
    {
        $this->hotel = $hotel;
    }

    //cache single hotel
    public function cacheSingleHotel()
    {
        if ($this->hotel) {
            if(Cache::has("hotels.{$this->hotel->id}")) {
                Cache::forget("hotels.{$this->hotel->id}");
            }

            return Cache::remember("hotels.{$this->hotel->id}", 60 * 60, function() 
            {
                \Log::info("Cache hotel: {$this->hotel->id}");
                if(Cache::has("hotels.{$this->hotel->id}")) {
                    return Cache::get("hotels.{$this->hotel->id}"); 
                }

                return $this->hotel->findOrFail($this->hotel->id);
            });
        }
    }

    //delete cache single hotel
    public function deleteCacheForSingleHotel()
    {
        if ($this->hotel) {
            if(Cache::has("hotels.{$this->hotel->id}")) {
                Cache::forget("hotels.{$this->hotel->id}");
            }
        }
    }

    //create translation function
    public function createTranslation($hotelId, $data) {
        $hotel = Hotel::findOrFail($hotelId);
        if ($hotel) {
            $slug = SlugService::createSlug(Hotel::class, 'slug', $data['name']);

            $dataTranslation = [
                'locale' => $data['locale'],
                'name' => $data['name'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'slug' => $slug,
                'website' => $data['website'],
                'description' => $data['description'],
                'meta_title' => $data['meta_title'] ?? '',
                'meta_description' => $data['meta_description'] ?? '',
                'meta_keywords' => $data['meta_keywords']  ?? '',
                'hotel_id' => $hotel->id,
            ];
            $hotel->setTranslation($dataTranslation);
            $hotel->save();
        }
    }
}