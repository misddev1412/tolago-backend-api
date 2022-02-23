<?php

namespace App\Services;
use Cache;
use App\Models\User;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class UserService
{
    //variable post
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

   //cache singleUser
    public function cacheSingleUser()
    {
        if ($this->user) {
            if(Cache::has("users.{$this->user->id}")) {
                Cache::forget("users.{$this->user->id}");
            }

            $cache = Cache::remember("users.{$this->user->id}", 60 * 60, function() 
            {
                \Log::info("Cache user: {$this->user->id}");
                if(Cache::has("users.{$this->user->id}")) {
                    return Cache::get("users.{$this->user->id}"); 
                }

                return $this->user;
            });

            \Log::info("Cache user: {$cache}");
            return $cache;
        }
    }

    //delete cache single post
    public function deleteCacheForSinglePost()
    {
        if ($this->user) {
            if(Cache::has("users.{$this->user->id}")) {
                Cache::forget("users.{$this->user->id}");
            }
        }
    }
}