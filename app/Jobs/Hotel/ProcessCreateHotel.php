<?php

namespace App\Jobs\Hotel;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Hotel;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\HotelService;
use App\Jobs\Media\ProcessImage;
use MeiliSearch\Client;

class ProcessCreateHotel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $dataCreate;
    protected $locale;
    protected $imageFileTmp;
    protected $ip;
    protected $userAgent;
    protected $images;
    protected $searchIndex = 'hotels';


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $dataCreate, $locale, $imageFileTmp, $images, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->dataCreate = $dataCreate;
        $this->locale = $locale;
        $this->imageFileTmp = $imageFileTmp;
        $this->images = $images;
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
        $dataCreate = [
            'user_id' => $this->userId,
            'name' => $this->dataCreate['name'],
            'address' => $this->dataCreate['address'],
            'phone' => $this->dataCreate['phone'] ?? '',
            'email' => $this->dataCreate['email'] ?? '',
            'website' => $this->dataCreate['website'] ?? '',
            'description' => $this->dataCreate['description'] ?? '',
            'lat' => $this->dataCreate['lat'] ?? 0,
            'lng' => $this->dataCreate['lng'] ?? 0,
            'status' => $this->dataCreate['status'] ?? 'inactive',
            'is_featured' => $this->dataCreate['is_featured'] ?? 0,
            'check_in' => $this->dataCreate['check_in'] ?? '14:00',
            'check_out' => $this->dataCreate['check_out'] ?? '12:00',
            'country_id' => $this->dataCreate['country_id'],
            'state_id' => $this->dataCreate['state_id'],
            'city_id' => $this->dataCreate['city_id'],
            'meta_tile' => $this->dataCreate['meta_tile'] ?? '',
            'meta_description' => $this->dataCreate['meta_description'] ?? '',
            'meta_keywords' => $this->dataCreate['meta_keywords'] ?? ''
        ];

        $createHotel = Hotel::create($dataCreate);
        if ($createHotel) {
            $activityLogService = new ActivityLogService();
            $activityLogService->createActivityLog($createHotel->user_id, 'create_hotel', $createHotel->id, 'hotels', 'success', $dataCreate, $this->ip, $this->userAgent);

            ProcessImage::dispatch($this->imageFileTmp, $this->userId, 'hotel', $createHotel->id, $this->ip, $this->userAgent);

            foreach ($this->images as $file) {
                if ($file) {
                    ProcessImage::dispatch($file, $this->userId, 'hotel', $createHotel->id, $this->ip, $this->userAgent, $mainImage = false);
    
                }
            }
            $dataCreate['locale'] = $this->locale;

            $hotelService = new HotelService($createHotel);
            $hotelService->createTranslation($createHotel->id, $dataCreate);
            
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
                $client->createIndex($this->searchIndex, ['primaryKey' => 'user_id']);
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
            'country_id',
            'state_id',
            'city_id',
            'status',
            'featured'
        ]);
        
    }
}
