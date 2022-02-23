<?php

namespace App\Jobs\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserSetting;
use App\Services\ActivityLogService;
use Notification;
use App\Notifications\EnableQrCode;

class ProcessEnableQrCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $userId;
    protected $ip;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $ip, $userAgent)
    {
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
        $user = User::find($this->userId);
        if ($user) {
            $dataUpdate = ['is_enable_2fa' => 1];
            UserSetting::where('user_id', $user->id)->update($dataUpdate);
            $payload = (object) $dataUpdate;
            $activityLogService = new ActivityLogService();
            $activityLogService->createActivityLog($user->id, 'enable_qr_code', $user->id, 'users', 'success', $payload, $this->ip, $this->userAgent);

            Notification::send($user, new EnableQrCode($user));

            // Broadcast::channel('user.' . $user->id)->broadcast(new \App\Events\User\QrCodeEnabled($user));
        }
    }
}
