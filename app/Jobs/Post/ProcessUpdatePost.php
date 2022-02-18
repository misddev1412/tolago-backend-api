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
use App\Jobs\Post\ProcessCachePost;
use App\Services\PostService;
use App\Jobs\Media\ProcessImage;
use App\Services\ActivityLogService;

class ProcessUpdatePost implements ShouldQueue
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
    protected $userId;
    protected $postId;
    protected $imageFileTmp;
    protected $ip;
    protected $userAgent;

    //construct post model
    public function __construct($userId, $request, $postId, $imageFileTmp = null, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->request = $request;
        $this->imageFileTmp = $imageFileTmp;
        $this->postId = $postId;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
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
            $dataUpdate = [
                'title' => $this->request['title'],
                'body'  => $this->request['body'],
                'category_id' => $this->request['category_id'],
                'status' => $this->request['status'] ?? 1,
                'meta_title' => $this->request['meta_title'] ?? '',
                'meta_description' => $this->request['meta_description'] ?? '',
                'meta_keywords' => $this->request['meta_keywords']  ?? '',
                'featured' => $this->request['featured'] ?? 0,
                'image' => null,
                
            ];
    
            $post = Post::findOrFail($this->postId);
            if ($post) {
    
                $activityLogService = new ActivityLogService();
                $activityLogService->createActivityLog($post->user_id, 'update_post', $post->id, 'posts', 'success', $this->request, $this->ip, $this->userAgent);
                
                $post->update($dataUpdate);
                
                $postService = new PostService($post);
                $postService->cacheSinglePost();

                Log::info(User::first()->name . ' updated post: ' . $post->title);
                Notification::send(User::first(), new NewPost($post));
                if ($this->imageFileTmp) {
                    ProcessImage::dispatch($this->imageFileTmp, $this->userId, 'post', $post->id, $this->ip, $this->userAgent);
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    
    }
}
