<?php

namespace App\Jobs\Room;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Hotel;
use App\Services\ActivityLogService;
use App\Services\HotelService;
use App\Jobs\Media\ProcessImage;
use App\Models\ImageRoom;
use App\Models\Room;
use App\Models\User;
use App\Services\RoomService;

class ProcessUpdateRoom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $userId;
    protected $dataUpdate;
    protected $locale;
    protected $thumbnail;
    protected $ip;
    protected $userAgent;
    protected $images;
    protected $deleteImages;
    protected $roomId;
    protected $searchIndex = 'rooms';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $roomId, $dataUpdate, $locale, $thumbnail, $images, $deleteImages, $ip, $userAgent)
    {
        //
        $this->userId = $userId;
        $this->dataUpdate = $dataUpdate;
        $this->locale = $locale;
        $this->thumbnail = $thumbnail;
        $this->images = $images;
        $this->deleteImages = $deleteImages;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->roomId = $roomId;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dataUpdate = [
            'name' => $this->dataUpdate['name'],
            'description' => $this->dataUpdate['description'] ?? '',
            'status' => $this->dataUpdate['status'] ?? 'inactive',
            'maxium_guest' => $this->dataUpdate['maxium_guest'] ?? 0,
            'hotel_id' => $this->dataUpdate['hotel_id'] ?? 0,
            'maxium_child' => $this->dataUpdate['maxium_child'] ?? 0,
            'square_feet' => $this->dataUpdate['square_feet'] ?? 0,
            'bed_type' => $this->dataUpdate['bed_type'] ?? '',
            'bed_quantity' => $this->dataUpdate['bed_quantity'] ?? 0,
            'bed_quantity_extra' => $this->dataUpdate['bed_quantity_extra'] ?? 0,
            'view_type' => $this->dataUpdate['view_type'] ?? '',
            'price' => $this->dataUpdate['price'] ?? 0,
            'currency_id' => $this->dataUpdate['currency_id'] ?? 0,
            
        ];

        $room = Room::find($this->roomId);
        if ($room) {
            $room->update($dataUpdate);
            $activityLogService = new ActivityLogService();
            $activityLogService->createActivityLog($this->userId, 'update_room', $this->roomId, 'rooms', 'success', $dataUpdate, $this->ip, $this->userAgent);

            ProcessImage::dispatch($this->thumbnail, $this->userId, 'room', $this->roomId, $this->ip, $this->userAgent);

            foreach ($this->images as $file) {
                if ($file) {
                    ProcessImage::dispatch($file, $this->userId, 'room', $this->roomId, $this->ip, $this->userAgent, $mainImage = false);
                }
            }

            foreach ($this->deleteImages as $image) {
                ImageRoom::where('image_id', $image)->delete();
            }
        }
    }
}
