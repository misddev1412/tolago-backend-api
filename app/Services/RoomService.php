<?php

namespace App\Services;
use Cache;
use App\Models\Room;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class RoomService
{
    //variable hotel
    protected $room;

    public function __construct($room)
    {
        $this->room = $room;
    }

    //cache single hotel
    public function cacheSingleRoom()
    {
        if ($this->room) {
            if(Cache::has("rooms.{$this->room->id}")) {
                Cache::forget("rooms.{$this->room->id}");
            }

            return Cache::remember("rooms.{$this->room->id}", 60 * 60, function() 
            {
                \Log::info("Cache room: {$this->room->id}");
                if(Cache::has("rooms.{$this->room->id}")) {
                    return Cache::get("rooms.{$this->room->id}"); 
                }

                return $this->room->findOrFail($this->room->id);
            });
        }
    }

    //delete cache single hotel
    public function deleteCacheForSingleRoom()
    {
        if ($this->hotel) {
            if(Cache::has("rooms.{$this->room->id}")) {
                Cache::forget("rooms.{$this->room->id}");
            }
        }
    }

    //create translation function
    public function createTranslation($roomId, $data) {
        $room = Room::findOrFail($roomId);
        if ($room) {
            $slug = SlugService::createSlug(Room::class, 'slug', $data['name']);

            $dataTranslation = [
                'locale' => $data['locale'],
                'name' => $data['name'],        
                'description' => $data['description'],
                'meta_title' => $data['meta_title'] ?? '',
                'meta_description' => $data['meta_description'] ?? '',
                'meta_keywords' => $data['meta_keywords']  ?? '',
                'room_id' => $room->id,
            ];
            $room->setTranslation($dataTranslation);
            $room->save();
        }
    }
}