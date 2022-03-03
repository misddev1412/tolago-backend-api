<?php

namespace App\Services;
use Cache;
use App\Models\Utility;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class UtilityService
{
    //variable hotel
    protected $utility;

    public function __construct($utility)
    {
        $this->utility = $utility;
    }

    //cache single hotel
    public function cacheSingleUtility()
    {
        if ($this->utility) {
            if(Cache::has("utilities.{$this->utility->id}")) {
                Cache::forget("utilities.{$this->utility->id}");
            }

            return Cache::remember("utilities.{$this->utility->id}", 60 * 60, function() 
            {
                \Log::info("Cache utility: {$this->utility->id}");
                if(Cache::has("utilities.{$this->utility->id}")) {
                    return Cache::get("utilities.{$this->utility->id}"); 
                }

                return $this->utility->findOrFail($this->utility->id);
            });
        }
    }

    //delete cache single hotel
    public function deleteCacheForSingleUtility()
    {
        if ($this->hotel) {
            if(Cache::has("utilities.{$this->utility->id}")) {
                Cache::forget("utilities.{$this->utility->id}");
            }
        }
    }

    //create translation function
    public function createTranslation($roomId, $data) {
        $utility = Utility::findOrFail($roomId);
        if ($utility) {
            $slug = SlugService::createSlug(Utility::class, 'slug', $data['name']);

            $dataTranslation = [
                'locale' => $data['locale'],
                'name' => $data['name'],        
                'description' => $data['description'],
                'meta_title' => $data['meta_title'] ?? '',
                'meta_description' => $data['meta_description'] ?? '',
                'meta_keywords' => $data['meta_keywords']  ?? '',
                'utility_id' => $utility->id,
            ];
            $utility->setTranslation($dataTranslation);
            $utility->save();
        }
    }
}