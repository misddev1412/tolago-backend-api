<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\PaymentMethod;
use App\Models\RoomPrice;
use App\Http\Requests\Room\CreateRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Http\Requests\Room\UpdatePriceRequest;
use Lang;
use App\Jobs\Room\ProcessCreateRoom;
use App\Jobs\Room\ProcessUpdateRoom;
use App\Jobs\Room\ProcessDeleteRoom;
use App\Jobs\Room\ProcessUpdateRoomPrice;

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
        Hotel::findOrFail($request->hotel_id);

        if (Gate::forUser(Auth::guard('api')->user())->denies('create-room')) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $file = $request->file('image');
        $fileName = Storage::disk('local')->put('tmp/images', $file);
        $fileNames = [];

        if (!Storage::disk('local')->exists($fileName)) {
            return Response::generateResponse(HttpStatusCode::INTERNAL_SERVER_ERROR, '', []);
        }
        if ($request->images) {
            foreach ($request->file('images') as $file) {
                $name = Storage::disk('local')->put('tmp/images', $file);
                $fileNames[] = $name;
            }
        }

        ProcessCreateRoom::dispatch(Auth::guard('api')->user()->id, $request->except('image', 'images'), Lang::getLocale(), $fileName, $fileNames, Helper::getClientIps(), Helper::getClientAgent());
        return Response::generateResponse(HttpStatusCode::CREATED, '', []);
    }

    //update
    public function update(UpdateRoomRequest $request, $id)
    {
        $room = Room::findOrFail($id);

        if (Gate::forUser(Auth::guard('api')->user())->denies('update-room', $room)) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $file = $request->file('image');
        $fileName = Storage::disk('local')->put('tmp/images', $file);
        $fileNames = [];
        $imagesDelete = [];
        if ($request->get('delete_images')) {
            $imagesDelete = explode(',', $request->get('delete_images'));
        }
        if (!Storage::disk('local')->exists($fileName)) {
            return Response::generateResponse(HttpStatusCode::INTERNAL_SERVER_ERROR, '', []);
        }
        if ($request->images) {
            foreach ($request->file('images') as $file) {
                $name = Storage::disk('local')->put('tmp/images', $file);
                $fileNames[] = $name;
            }
        }

        ProcessUpdateRoom::dispatch(Auth::guard('api')->user()->id, $id, $request->except('image', 'images'), Lang::getLocale(), $fileName, $fileNames, $imagesDelete, Helper::getClientIps(), Helper::getClientAgent());
        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }

    public function show($id) {
        $room = $this->roomRepository->findOrFailWithAll($id);

        if (Gate::forUser(Auth::guard('api')->user())->denies('view-room', $room)) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }
        
        return Response::generateResponse(HttpStatusCode::OK, '', $room);
    }

    public function updatePrice(UpdatePriceRequest $request, $id) {
        $room = Room::findOrFail($id);
        $roomPrice = RoomPrice::select('start_date', 'end_date')->where('room_id', $room->id)->get();
        $dateRange = $roomPrice->toArray();
        // dd($dateRange);
        foreach ($dateRange as $date) {
            if ($request->start_date >= $date['start_date'] && $request->start_date <= $date['end_date'] || $request->end_date >= $date['start_date'] && $request->end_date <= $date['end_date']) {
                return Response::generateResponse(HttpStatusCode::BAD_REQUEST, __(''), []);
            }
        }
        
        if (Gate::forUser(Auth::guard('api')->user())->denies('update-room', $room)) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $deletePriceIds = explode(',', $request->delete_price_ids);

        ProcessUpdateRoomPrice::dispatch(Auth::guard('api')->user()->id, $id, $request->only('start_date', 'end_date', 'price', 'currency_id'),  $deletePriceIds, Helper::getClientIps(), Helper::getClientAgent());
        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }
}
