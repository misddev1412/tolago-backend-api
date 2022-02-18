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
use App\Services\ActivityLogService;
use App\Models\UserCount;

class ProcessDeletePost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $postId;
    protected $userId;
    protected $ip;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    //construct post model
    public function __construct($postId, $userId, $ip, $userAgent)
    {
        $this->postId = $postId;
        $this->userId = $userId;
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
            $post = Post::findOrFail($this->postId);
            if ($post) {
                
                UserCount::where('user_id', $post->user_id)->update(
                    [
                        'total_post_deleted' => \DB::raw('total_post_deleted + 1'),
                        'total_post_deleted_by_me' => $post->user_id == $this->userId ? \DB::raw('total_post_deleted_by_me + 1') : \DB::raw('total_post_deleted_by_me'),
                    ]
                );

                $postService = new PostService($post);
                $postService->deleteCacheForSinglePost();

                $activityLogService = new ActivityLogService();
                $activityLogService->createActivityLog($post->user_id, 'delete_post', $post->id, 'posts', 'success', [], $this->ip, $this->userAgent);
                
                $deleted = $post->delete();
            
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    
    }
}
