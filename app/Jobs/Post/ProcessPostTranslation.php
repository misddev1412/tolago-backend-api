<?php

namespace App\Jobs\Post;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use Storage;
use Log;
use Illuminate\Http\File;
use Notification;
use App\Notifications\NewPost;
use App\Models\User;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use App\Services\PostService;

class ProcessPostTranslation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    //construct post model
    protected $post;
    protected $request;
    protected $postId;

    //construct post model
    public function __construct($postId, $request)
    {
        $this->postId = $postId;
        $this->request = $request;
    }



    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        try {
            $post = Post::findOrFail($this->postId);
            if ($post) {
                $postService = new PostService($post);
                $postService->cacheSinglePost();
                $postService->createTranslations($post->id, $this->request);

                Log::info(User::first()->name . ' created a new post: ' . $post->title);
                Notification::send(User::first(), new NewPost($post));
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    
    }
}
