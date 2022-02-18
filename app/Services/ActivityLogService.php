<?php

namespace App\Services;
use Cache;
use App\Models\Post;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Image;
use App\Models\Image as ImageModel;
use Auth;
use Storage;
use Illuminate\Http\File;
use App\Models\ImagePost;
use App\Models\ImageMessage;
use App\Models\ActivityLog;

class ActivityLogService
{
    //reate activity log
    public function createActivityLog($userId, $actionType, $actionId, $actionTable, $status, $request, $ipAddress, $userAgent)
    {
        $payload = json_encode($request, 1);

        $activityLog = new ActivityLog();
        $activityLog->user_id = $userId;
        $activityLog->action_type = $actionType;
        $activityLog->action_id = $actionId;
        $activityLog->action_table = $actionTable;
        $activityLog->status = $status;
        $activityLog->ip_address = $ipAddress;
        $activityLog->user_agent = $userAgent;
        $activityLog->payload = $payload;
        $activityLog->save();
    }
}