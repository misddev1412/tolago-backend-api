<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Notification;
use Auth;
use Carbon\Carbon;

class EventStreamController extends Controller
{
    //
    public function getEventStreamNotification() {


        // $data = Notification::unread()->byNotifiableId(Auth::guard('api')->user()->id)->first();
        $notifications = Notification::notSentSse()->byNotifiableId(2)->where('created_at', '>=', Carbon::now()->subSeconds(5))->get();
        if ($notifications) {
            foreach ($notifications as $notification) {
                Notification::find($notification->id)->update(['is_sent_sse' => true]);
            }
        }

        $response = new StreamedResponse(); 
        $response->setCallback(function () use ($notifications){
            if ($notifications) {
                echo 'data: ' .json_encode($notifications) . "\n\n";
            } else {
                echo 'data: ' . json_encode([]) . "\n\n";
            }
             //echo "retry: 100\n\n"; // no retry would default to 3 seconds.
             //echo "data: Hello There\n\n";
            ob_flush();
            flush();
             //sleep(10);
            usleep(200000);
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cach-Control', 'no-cache');
        $response->send();
    }

}
