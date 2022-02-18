<?php

namespace App\Jobs\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Services\ActivityLogService;

class ProcessSignin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;
    protected $status;
    protected $ip;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $status, $ip, $userAgent)
    {
        $this->request = $request;
        $this->status = $status;
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
        $user = User::where('email', $this->request['email'])->first();
        if ($user) {
            $activityLogService = new ActivityLogService();
            $status = $this->status ? 'success' : 'failed';
            $activityLogService->createActivityLog($user->id, 'signin', $user->id, 'users', $status, $this->request, $this->ip, $this->userAgent);
        }
    
    }
}
