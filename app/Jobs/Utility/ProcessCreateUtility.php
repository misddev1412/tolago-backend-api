<?php

namespace App\Jobs\Utility;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;
use App\Models\Utility;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\UtilityService;
use App\Jobs\Media\ProcessImage;
use MeiliSearch\Client;

class ProcessCreateUtility implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //construct utility model
    protected $utility;
    protected $request;
    protected $userId;
    protected $imageFileTmp;
    protected $locale;
    protected $ip;
    protected $userAgent;
    protected $searchIndex = 'utilities';

    //construct utility model
    public function __construct($userId, $request, $locale, $imageFileTmp, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->request = $request;
        $this->imageFileTmp = $imageFileTmp;
        $this->locale = $locale;
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
        try {
            $dataCreate = [
                'name' => $this->request['name'],
                'description' => $this->request['description'],
                'user_id' => $this->userId,
                'status' => $this->request['status'] ?? 1,
                'type' => $this->request['type'],

            ];

            $utility = Utility::create($dataCreate);

            if ($utility) {

                $activityLogService = new ActivityLogService();
                $activityLogService->createActivityLog($utility->user_id, 'create_utility', $utility->id, 'utilities', 'success', $this->request, $this->ip, $this->userAgent);

                ProcessImage::dispatch($this->imageFileTmp, $this->userId, 'utility', $utility->id, $this->ip, $this->userAgent);

                $utilityService = new UtilityService($utility);
                $utilityService->cacheSingleUtility();


                $this->request['locale'] = $this->locale;

                $utilityService->createTranslation($utility->id, $this->request);

                $this->initIndexMeiliSearchEngine();
                $this->addSortAbleToSearchEngine();
                $this->addFilterAbleToSearchEngine();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
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
            'status',
            'featured'
        ]);

    }
}
