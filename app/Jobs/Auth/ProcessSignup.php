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
use MeiliSearch\Client;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\UserService;

class ProcessSignup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataCreate;
    protected $request;
    protected $ip;
    protected $userAgent;
    protected $searchIndex = 'users';
    protected $userRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataCreate, $request, $userRepository, $ip, $userAgent)
    {
        $this->dataCreate = $dataCreate;
        $this->request = $request;
        $this->userRepository = $userRepository;
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
        $user = $this->userRepository->store($this->dataCreate);
        
        if ($user) {
            $userService = new UserService(User::findOrFail($user->id));
            $userService->cacheSingleUser();

            UserSetting::create([
                'user_id' => $user->id,
            ]);
    
            UserCount::create([
                'user_id' => $user->id,
            ]);

            Role::where('slug', 'user')->first()->users()->attach($user->id);

            $activityLogService = new ActivityLogService();
            $activityLogService->createActivityLog($user->id, 'signup', $user->id, 'users', 'success', $this->request, $this->ip, $this->ip, $this->userAgent);
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
            'email',
            'phone_number',
            'username',
            'status'
        ]);
        
    }
}
