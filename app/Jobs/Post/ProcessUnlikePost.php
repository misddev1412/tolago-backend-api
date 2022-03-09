<?php

namespace App\Jobs\Post;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ActivityLogService;
use App\Models\Post;
use App\Models\Like;

class ProcessUnlikePost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $postId;
    protected $userId;
    protected $ip;
    protected $userAgent;
    protected $request;

    public function __construct($postId, $userId, $request, $ip, $userAgent)
    {
        $this->postId = $postId;
        $this->userId = $userId;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->request = $request;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $activityLogService = new ActivityLogService();
        $activityLogService->createActivityLog($this->userId, 'like_post', $this->postId, 'posts', 'success', $this->request, $this->ip, $this->userAgent);

        $post = Post::find($this->postId);
        Like::where('user_id', $this->userId)->where('likeable_id', $this->postId)->where('likeable_type', 'App\Models\Post')->delete();


    }
}
