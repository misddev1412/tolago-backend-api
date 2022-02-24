<?php

namespace App\Jobs\Room;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\RoomService;
use App\Jobs\Media\ProcessImage;
use MeiliSearch\Client;

class ProcessCreateRoom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $dataCreate;
    protected $locale;
    protected $imageFileTmp;
    protected $ip;
    protected $userAgent;
    protected $images;
    protected $searchIndex = 'rooms';


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
        //
        $dataCreate = [
            'name' => $this->dataCreate['name'],
            'description' => $this->dataCreate['description'],
            'status' => $this->dataCreate['status'],
            'maxium_guest' => $this->dataCreate['maxium_guest'],
            'maxium_child' => $this->dataCreate['maxium_child'],
            'square_feet' => $this->dataCreate['square_feet'],
            'bed_type' => $this->dataCreate['bed_type'],
            'bed_quantity' => $this->dataCreate['bed_quantity'],
            'bed_quantity_extra' => $this->dataCreate['bed_quantity_extra'],
            'view_type' => $this->dataCreate['view_type'],
            'price' => $this->dataCreate['price'],
            'currency_id' => $this->dataCreate['currency_id'],
            'hotel_id' => $this->dataCreate['hotel_id'],
            'user_id' => $this->userId
        ];

        $createRoom = Room::create($dataCreate);
        if ($createRoom) {
            $activityLogService = new ActivityLogService();
            $activityLogService->createActivityLog($this->userId, 'create_room', $createRoom->id, 'rooms', 'success', $dataCreate, $this->ip, $this->userAgent);

            ProcessImage::dispatch($this->imageFileTmp, $this->userId, 'room', $createRoom->id, $this->ip, $this->userAgent);

            foreach ($this->images as $file) {
                if ($file) {
                    ProcessImage::dispatch($file, $this->userId, 'room', $createRoom->id, $this->ip, $this->userAgent, $mainImage = false);
                }
            }
            $dataCreate['locale'] = $this->locale;

            $roomService = new RoomService($createRoom);
            $roomService->createTranslation($createRoom->id, $dataCreate);

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
            'hotel_id',
            'status',
        ]);
        
    }
}
