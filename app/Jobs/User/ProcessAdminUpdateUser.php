<?php

namespace App\Jobs\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ActivityLogService;
use App\Models\User;
use App\Jobs\Media\ProcessImage;

class ProcessAdminUpdateUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $data;
    protected $ip;
    protected $userAgent;
    protected $tmpAvatarFile;
    protected $adminId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $data, $tmpAvatarFile, $adminId, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->data = $data;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->tmpAvatarFile = $tmpAvatarFile;
        $this->adminId = $adminId;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::findOrFail($this->userId);
        if ($user) {
            if ($this->tmpAvatarFile) {
                ProcessImage::dispatch($this->tmpAvatarFile, $this->userId, 'user', $this->userId, $this->ip, $this->userAgent);
            }

            User::findOrFail($this->userId)->update($this->data);

            $activityLogService = new ActivityLogService();
            $activityLogService->createActivityLog($this->adminId, 'update_profile_by_admin', $user->id, 'users', 'success', $this->data, $this->ip, $this->userAgent);
        }

    }
}
