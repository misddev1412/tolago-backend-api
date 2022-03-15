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
use MeiliSearch\Client;
use Notification;
use App\Notifications\InviteFriend;

class ProcessAcceptFriend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;
    private $friendId;
    protected $searchIndex = 'user_friends';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $friendId)
    {
        $this->userId = $userId;
        $this->friendId = $friendId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userFriend = UserFriend::where('user_id', $this->friendId)
            ->where('friend_id', $this->userId)
            ->first();

        $userFriend->status = UserFriend::ACCEPTED;
        $userFriend->save();

        if ($userFriend) {
            UserFriend::create([
                'user_id' => $this->userId,
                'friend_id' => $this->friendId,
                'status' => UserFriend::ACCEPTED,
            ]);

            $this->initIndexMeiliSearchEngine();
            $this->addSortAbleToSearchEngine();
            $this->addFilterAbleToSearchEngine();

        }
    }

    protected function initIndexMeiliSearchEngine()
    {
        $client = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        try {
            $index = $client->index($this->searchIndex)->fetchRawInfo();

        } catch (\MeiliSearch\Exceptions\ApiException $e) {
            if ($e->getCode() == 404) {
                $client->createIndex($this->searchIndex, ['primaryKey' => 'id']);
            }
        }



    }

    protected function addSortAbleToSearchEngine()
    {
        $client = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $index = $client->getIndex($this->searchIndex);
        $index->updateSortableAttributes([
            'created_at'
        ]);

    }


    protected function addFilterAbleToSearchEngine()
    {
        $client = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $index = $client->getIndex($this->searchIndex);

        $index->updateFilterableAttributes([
            'user_id',
            'friend_id',
            'status'
        ]);

    }
}
