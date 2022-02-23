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

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileName;
    protected $userId;
    protected $table;
    protected $objectId;
    protected $ip;
    protected $userAgent;
    protected $mainImage;

    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileName, $userId, $table, $objectId, $ip, $userAgent, $mainImage = true) {
        $this->fileName = $fileName;
        $this->userId = $userId;
        $this->table = $table;
        $this->objectId = $objectId;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->mainImage = $mainImage;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $media = MediaService::commonImage($this->fileName, $this->userId, $this->table, $this->objectId, $this->mainImage);
        $activityLogService = new ActivityLogService();
        $activityLogService->createActivityLog($this->userId, 'upload_image', $media->id, 'images', 'success', [$this->fileName], $this->ip, $this->userAgent);

        $userCount = UserCount::where('user_id', $this->userId)->first();
        if ($userCount) {
            $userCount->total_image_uploaded = $userCount->total_image_uploaded + 1;
            $userCount->save();
        }
        
    }
}
