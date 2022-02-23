<?php

namespace App\Jobs\Hotel;

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
use App\Models\ImageHotel;

class ProcessUpdateHotel implements ShouldQueue
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
    protected $hotelId;
    protected $searchIndex = 'hotels';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $hotelId, $dataUpdate, $locale, $thumbnail, $images, $deleteImages, $ip, $userAgent)
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
        $this->hotelId = $hotelId;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $dataUpdate = [
            'user_id' => $this->userId,
            'name' => $this->dataUpdate['name'],
            'address' => $this->dataUpdate['address'],
            'phone' => $this->dataUpdate['phone'] ?? '',
            'email' => $this->dataUpdate['email'] ?? '',
            'website' => $this->dataUpdate['website'] ?? '',
            'description' => $this->dataUpdate['description'] ?? '',
            'lat' => $this->dataUpdate['lat'] ?? 0,
            'lng' => $this->dataUpdate['lng'] ?? 0,
            'status' => $this->dataUpdate['status'] ?? 'inactive',
            'is_featured' => $this->dataUpdate['is_featured'] ?? 0,
            'check_in' => $this->dataUpdate['check_in'] ?? '14:00',
            'check_out' => $this->dataUpdate['check_out'] ?? '12:00',
            'country_id' => $this->dataUpdate['country_id'],
            'state_id' => $this->dataUpdate['state_id'],
            'city_id' => $this->dataUpdate['city_id'],
            'meta_tile' => $this->dataUpdate['meta_tile'] ?? '',
            'meta_description' => $this->dataUpdate['meta_description'] ?? '',
            'meta_keywords' => $this->dataUpdate['meta_keywords'] ?? ''
        ];

        $hotel = Hotel::find($this->hotelId);
        if ($hotel) {
            $hotel->update($dataUpdate);
            $activityLogService = new ActivityLogService();
            $activityLogService->createActivityLog($this->userId, 'update_hotel', $this->hotelId, 'hotels', 'success', $dataUpdate, $this->ip, $this->userAgent);

            ProcessImage::dispatch($this->thumbnail, $this->userId, 'hotel', $this->hotelId, $this->ip, $this->userAgent);

            foreach ($this->images as $file) {
                if ($file) {
                    ProcessImage::dispatch($file, $this->userId, 'hotel', $this->hotelId, $this->ip, $this->userAgent, $mainImage = false);
                }
            }

            foreach ($this->deleteImages as $image) {
                ImageHotel::where('image_id', $image)->delete();
            }
        }

    }
}
