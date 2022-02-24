<?php

namespace App\Jobs\Room;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Room;
use App\Models\RoomPrice;

class ProcessUpdateRoomPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $id;
    protected $dataPrice;
    protected $ip;
    protected $userAgent;
    protected $deletePriceIds;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $id, $dataPrice, $deletePriceIds, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->id = $id;
        $this->dataPrice = $dataPrice;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->deletePriceIds = $deletePriceIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $room = Room::findOrFail($this->id);
        if ($room) {
            if (count($this->deletePriceIds) > 0) {
                foreach ($this->deletePriceIds as $priceId) {
                    $price = RoomPrice::find($priceId);
                    if ($price) {
                        $price->delete();
                    }
                }
            }

            \Log::info($this->dataPrice['start_date']);
            RoomPrice::create(
                [
                    'room_id' => $room->id,
                    'price' => $this->dataPrice['price'],
                    'currency_id' => $this->dataPrice['currency_id'],
                    'start_date' => $this->dataPrice['start_date'],
                    'end_date' => $this->dataPrice['end_date'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );
        }
    }
}
