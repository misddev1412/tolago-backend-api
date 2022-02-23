<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use App\Models\Message;
use App\Helpers\Response;
use App\Helpers\Helper;
use App\Enum\HttpStatusCode;
use Gate;
use Auth;
use App\Events\MessagePosted;
use App\Models\User;
use App\Jobs\Chat\ProcessCreateMessage;
use Storage;
class ChatController extends Controller
{

    
    //construct chat controller with message model
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function chat() {
        $firebase = new FirebaseService();
        $database = $firebase->database();
        return response()->json($value);
    }

    //store chat message to database and return generated response
    public function store(Request $request)
    {
        if ($request->recipient_id == Auth::guard('api')->user()->id) {
            return Response::generateResponse(HttpStatusCode::BAD_REQUEST, 'You can not send messages to yourself', []);
        }
        $dataCreate = [
            'message' => $request->message,
            'sender_id' => Auth::guard('api')->user()->id,
            'recipient_id' => $request->recipient_id,
        ];
        User::findOrFail($request->recipient_id);

        $fileNames = [];

        if ($request->file('images')) {
            foreach($request->file('images') as $image) {
                $fileNames[] = Storage::disk('local')->put('tmp/images', $image);
            }
        }

        ProcessCreateMessage::dispatch($dataCreate, $fileNames, Helper::getClientIps(), Helper::getClientAgent());

        return Response::generateResponse(HttpStatusCode::CREATED, '', []);
    }
}
