<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UtilityController extends Controller
{
    public function store(CreateRoomRequest $request)
    {
        Hotel::findOrFail($request->hotel_id);

        if (Gate::forUser(Auth::guard('api')->user())->denies('create-utility')) {
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
}
