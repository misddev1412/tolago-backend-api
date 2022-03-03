<?php

namespace App\Jobs\Utility;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Utility;
use App\Services\UtilityService;
use App\Jobs\Media\ProcessImage;
use MeiliSearch\Client;
use App\Services\ActivityLogService;

class ProcessUpdateUtility implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $utility;
    protected $request;
    protected $userId;
    protected $utilityId;
    protected $imageFileTmp;
    protected $ip;
    protected $userAgent;

    //construct utility model
    public function __construct($userId, $utilityId, $request, $imageFileTmp = null, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->request = $request;
        $this->imageFileTmp = $imageFileTmp;
        $this->utilityId = $utilityId;
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
            $dataUpdate = [
                'name' => $this->request['name'],
                'description' => $this->request['description'],
                'status' => $this->request['status'] ?? 1,
                'type' => $this->request['type'],
                
                
            ];
    
            $utility = Utility::findOrFail($this->utilityId);
            if ($utility) {
    
                $activityLogService = new ActivityLogService();
                $activityLogService->createActivityLog($utility->user_id, 'update_utility', $utility->id, 'utilities', 'success', $this->request, $this->ip, $this->userAgent);
                
                $utility->update($dataUpdate);
                
                $utilityService = new UtilityService($utility);
                $utilityService->cacheSingleUtility();

                if ($this->imageFileTmp) {
                    ProcessImage::dispatch($this->imageFileTmp, $this->userId, 'utility', $utility->id, $this->ip, $this->userAgent );
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    
    }
}
