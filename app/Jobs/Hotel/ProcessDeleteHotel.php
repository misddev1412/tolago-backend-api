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
use App\Models\ImageHotel;
use App\Models\Room;

class ProcessDeleteHotel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $userId;
    protected $hotelId;
    protected $ip;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $hotelId, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->hotelId = $hotelId;
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
        Hotel::where('id', $this->hotelId)->delete();
        ImageHotel::where('hotel_id', $this->hotelId)->delete();
        Room::where('hotel_id', $this->hotelId)->delete();
        $activityLogService = new ActivityLogService();
        $activityLogService->createActivityLog($this->userId, 'delete_hotel', $this->hotelId, 'hotels', 'success', [], $this->ip, $this->userAgent);
    }
}
