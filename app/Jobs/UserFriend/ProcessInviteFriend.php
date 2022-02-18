<?php

namespace App\Jobs\UserFriend;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\UserFriend;
use App\Models\User;
use Notification;
use App\Notifications\InviteFriend;

class ProcessInviteFriend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;
    private $friendId;
    private $status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $friendId, $status)
    {
        $this->userId = $userId;
        $this->friendId = $friendId;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userFriend = UserFriend::create([
            'user_id' => $this->userId,
            'friend_id' => $this->friendId,
            'status' => $this->status
        ]);
        \Log::info($userFriend);
        if ($userFriend) {
            $user = User::find($this->userId);
            $friend = User::find($this->friendId);
            Notification::send($friend, new InviteFriend($user, $friend));
            
        }
    }
}
