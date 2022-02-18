<?php

namespace App\Services;
use Cache;
use App\Models\Post;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PostService
{
    //variable post
    protected $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    //cache single post
    public function cacheSinglePost()
    {


        if ($this->post) {

            if(Cache::has("posts.{$this->post->id}")) {
                Cache::forget("posts.{$this->post->id}");
            }

            return Cache::remember("posts.{$this->post->id}", 60 * 60, function() 
            {
                \Log::info("Cache post: {$this->post->id}");
                if(Cache::has("posts.{$this->post->id}")) {
                    return Cache::get("posts.{$this->post->id}"); 
                }

                return $this->post->findOrFailWithAll($this->post->id);
            });
        }
    }

    //delete cache single post
    public function deleteCacheForSinglePost()
    {
        if ($this->post) {
            if(Cache::has("posts.{$this->post->id}")) {
                Cache::forget("posts.{$this->post->id}");
            }
        }
    }

    //create translation function
    public function createTranslation($postId, $data) {
        $post = Post::findOrFail($postId);
        if ($post) {
            $slug = SlugService::createSlug(Post::class, 'slug', $data['title']);

            $dataTranslation = [
                'locale' => $data['locale'],
                'title' => $data['title'],
                'body' => $data['body'],
                'slug' => $slug,
                'meta_title' => $data['meta_title'] ?? '',
                'meta_description' => $data['meta_description'] ?? '',
                'meta_keywords' => $data['meta_keywords']  ?? '',
                'post_id' => $post->id,
            ];
            $post->setTranslation($dataTranslation);
            $post->save();
        }
    }
}