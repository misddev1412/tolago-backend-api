<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hotel;
use App\Models\PaymentMethod;
use App\Models\Room;
use App\Http\Requests\Hotel\CreateHotelRequest;
use App\Http\Requests\Hotel\UpdateHotelRequest;
use Lang;
use App\Jobs\Hotel\ProcessCreateHotel;
use App\Jobs\Hotel\ProcessUpdateHotel;
use App\Jobs\Hotel\ProcessDeleteHotel;
use Auth;
use Gate;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;
use App\Helpers\Helper;
use App\Repositories\Hotel\HotelRepositoryInterface;
class HotelController extends Controller
{
    protected $user;
    protected $hotel;
    protected $paymentMethod;
    protected $room;
    protected $hotelRepository;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //construct for Hotel models
    public function __construct(Hotel $hotel, User $user, PaymentMethod $paymentMethod, Room $room, HotelRepositoryInterface $hotelRepository)
    {
        $this->hotel = $hotel;
        $this->user = $user;
        $this->paymentMethod = $paymentMethod;
        $this->room = $room;
        $this->hotelRepository = $hotelRepository;
    }
    
    

    //store function
    public function store(CreateHotelRequest $request)
    {
        if (Gate::forUser(Auth::guard('api')->user())->denies('create-hotel')) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $file = $request->file('image');
        $fileName = Storage::disk('local')->put('tmp/images', $file);
        $fileNames = [];
        
        if (!Storage::disk('local')->exists($fileName)) {
            return Response::generateResponse(HttpStatusCode::INTERNAL_SERVER_ERROR, '', []);
        }

        if ($request->file('images')) {
            foreach($request->file('images') as $image) {
                $fileNames[] = Storage::disk('local')->put('tmp/images', $image);
            }
        }

        ProcessCreateHotel::dispatch(Auth::guard('api')->user()->id, $request->except('image'), Lang::getLocale(), $fileName, $fileNames, Helper::getClientIps(), Helper::getClientAgent());

        return Response::generateResponse(HttpStatusCode::CREATED, '', []);
    }

    public function index(Request $request)
    {

        $hotels = $this->hotelRepository->index($request);

        return Response::generateResponse(HttpStatusCode::OK, '', $hotels);
    }

    public function update(UpdateHotelRequest $request, $id) {
        if (!Hotel::findOrFail($id)) {
            return Response::generateResponse(HttpStatusCode::NOT_FOUND, '', []);
        }

        if (Gate::forUser(Auth::guard('api')->user())->denies('update-hotel', Hotel::findOrFail($id))) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $file = $request->file('image');
        $fileName = Storage::disk('local')->put('tmp/images', $file);
        $fileNames = [];

        if (!Storage::disk('local')->exists($fileName)) {
            return Response::generateResponse(HttpStatusCode::INTERNAL_SERVER_ERROR, '', []);
        }

        if ($request->file('images')) {
            foreach($request->file('images') as $image) {
                $fileNames[] = Storage::disk('local')->put('tmp/images', $image);
            }
        }

        $imagesDelete = [];
        if ($request->get('delete_images')) {
            $imagesDelete = explode(',', $request->get('delete_images'));
        }

        ProcessUpdateHotel::dispatch(Auth::guard('api')->user()->id, $id, $request->except('image', 'images'), Lang::getLocale(), $fileName, $fileNames, $imagesDelete, Helper::getClientIps(), Helper::getClientAgent());

        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }

    public function destroy($id) {
        if (Gate::forUser(Auth::guard('api')->user())->denies('delete-hotel', Hotel::findOrFail($id))) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $hotel = Hotel::findOrFail($id);
        if (!$hotel) {
            return Response::generateResponse(HttpStatusCode::NOT_FOUND, '', []);
        }

        ProcessDeleteHotel::dispatch(Auth::guard('api')->user()->id, $id, Helper::getClientIps(), Helper::getClientAgent());
        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }

    public function show($id) {
        $hotel = $this->hotelRepository->findOrFailWithAll($id);

        if (Gate::forUser(Auth::guard('api')->user())->denies('view-hotel', $hotel)) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }
        
        return Response::generateResponse(HttpStatusCode::OK, '', $hotel);
    }
}
