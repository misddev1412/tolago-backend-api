<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\PaymentMethod;
use App\Http\Requests\Room\CreateRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use Lang;
use App\Jobs\Room\ProcessCreateRoom;
use App\Jobs\Room\ProcessUpdateRoom;
use App\Jobs\Room\ProcessDeleteRoom;

use Auth;
use Gate;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;
use App\Helpers\Helper;
use App\Repositories\Room\RoomRepositoryInterface;

class RoomController extends Controller
{
    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }
    
    //index
    public function index(Request $request)
    {
        $rooms = $this->roomRepository->index($request);
        return Response::generateResponse(HttpStatusCode::OK, '', $rooms);
    }

    //store
    public function store(CreateRoomRequest $request)
    {
        if (Gate::forUser(Auth::guard('api')->user())->denies('create-room')) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $file = $request->file('image');
        $fileName = Storage::disk('local')->put('tmp/images', $file);
        $fileNames = [];

        if (!Storage::disk('local')->exists($fileName)) {
            return Response::generateResponse(HttpStatusCode::INTERNAL_SERVER_ERROR, '', []);
        }

        foreach ($request->file('images') as $file) {
            $name = Storage::disk('local')->put('tmp/images', $file);
            $fileNames[] = $name;
        }

        ProcessCreateRoom::dispatch(Auth::guard('api')->user(), $request->except('image', 'images'), $fileName, $fileNames, Helper::getClientIps(), Helper::getClientAgent());
        return Response::generateResponse(HttpStatusCode::OK, '', $room);
    }
}
