<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //loop db insert
        for ($i = 0; $i < 10; $i++) {
            $post = new \App\Models\Post();
            $post->title = 'Post title ' . $i;
            $post->body = 'Post body ' . $i;
            $post->user_id = 1;
            $post->category_id = 1;
            $post->image = 'image.jpg';
            $post->slug = 'post-title-' . $i;
            $post->status = 1;
            $post->meta_title = 'Meta title ' . $i;
            $post->meta_description = 'Meta description ' . $i;
            $post->meta_keywords = 'Meta keywords ' . $i;
            $post->featured = 1;
            $post->save();
        }
    }
}
