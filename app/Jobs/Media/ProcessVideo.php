<?php

namespace App\Jobs\Media;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\MediaService;
use App\Services\ActivityLogService;
use App\Models\UserCount;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileName;
    protected $userId;
    protected $table;
    protected $objectId;
    protected $ip;
    protected $userAgent;
    protected $mainVideo;

    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileName, $userId, $table, $objectId, $ip, $userAgent, $mainVideo = true) {
        $this->fileName = $fileName;
        $this->userId = $userId;
        $this->table = $table;
        $this->objectId = $objectId;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->mainVideo = $mainVideo;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $media = MediaService::commonVideo($this->fileName, $this->userId, $this->table, $this->objectId, $this->mainVideo);
        $activityLogService = new ActivityLogService();
        $activityLogService->createActivityLog($this->userId, 'upload_video', $media->id, 'videos', 'success', [$this->fileName], $this->ip, $this->userAgent);

        $userCount = UserCount::where('user_id', $this->userId)->first();
        if ($userCount) {
            $userCount->total_videos_uploaded = $userCount->total_videos_uploaded + 1;
            $userCount->save();
        }
        
    }
}
