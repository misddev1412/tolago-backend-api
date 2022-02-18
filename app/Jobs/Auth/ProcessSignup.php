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
use App\Models\UserSetting;
use App\Models\UserCount;
use App\Models\UserRole;
use App\Models\Role;

class ProcessSignup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataCreate;
    protected $request;
    protected $ip;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataCreate, $request, $ip, $userAgent)
    {
        $this->dataCreate = $dataCreate;
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
        $user = User::create($this->dataCreate);
        
        if ($user) {
            UserSetting::create([
                'user_id' => $user->id,
            ]);
    
            UserCount::create([
                'user_id' => $user->id,
            ]);

            Role::where('slug', 'user')->first()->users()->attach($user->id);

            $activityLogService = new ActivityLogService();
            $activityLogService->createActivityLog($user->id, 'signup', $user->id, 'users', 'success', $this->request, $this->ip, $this->ip, $this->userAgent);
        } 
    }
}
