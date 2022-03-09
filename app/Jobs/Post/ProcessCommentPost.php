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
use App\Models\Comment;
class ProcessCommentPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $postId;
    protected $userId;
    protected $request;
    protected $ip;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($postId, $userId, $request, $ip, $userAgent)
    {
        $this->postId = $postId;
        $this->userId = $userId;
        $this->request = $request;
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
        $activityLogService = new ActivityLogService();
        $activityLogService->createActivityLog($this->userId, 'comment_post', $this->postId, 'posts', 'success', $this->request, $this->ip, $this->userAgent);

        $post = Post::find($this->postId);
        Comment::create([
            'commentable_id' => $this->postId,
            'user_id' => $this->userId,
            'comment' => $this->request['comment_text'],
            'commentable_type' => 'App\Models\Post',
            'status' => 1,
        ]);

    }
}
