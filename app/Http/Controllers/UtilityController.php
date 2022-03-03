<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Lang;

use Auth;
use Gate;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;
use App\Helpers\Helper;
use App\Repositories\Utility\UtilityRepositoryInterface;

use App\Jobs\Utility\ProcessCreateUtility;
use App\Jobs\Utility\ProcessUpdateUtility;
use App\Jobs\Utility\ProcessDeleteUtility;

use App\Http\Requests\Utility\CreateUtilityRequest;
use App\Http\Requests\Utility\UpdateUtilityRequest;
use App\Models\Utility;

class UtilityController extends Controller
{
    
    protected $utilityRepository;
    //construct 
    public function __construct(UtilityRepositoryInterface $utilityRepository)
    {
        $this->utilityRepository = $utilityRepository;
    }


    public function store(CreateUtilityRequest $request)
    {
        if (!Gate::forUser(Auth::guard('api')->user())->denies('create-utility')) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $file = $request->file('image');
        $fileName = Storage::disk('local')->put('tmp/images', $file);

        if (!Storage::disk('local')->exists($fileName)) {
            return Response::generateResponse(HttpStatusCode::INTERNAL_SERVER_ERROR, '', []);
        }
    

        ProcessCreateUtility::dispatch(Auth::guard('api')->user()->id, $request->except('image'), Lang::getLocale(), $fileName, Helper::getClientIps(), Helper::getClientAgent());
        return Response::generateResponse(HttpStatusCode::CREATED, '', []);
    }

    public function update(UpdateUtilityRequest $request, $id)
    {
        $utility = Utility::findOrFail($id);

        if (!Gate::forUser(Auth::guard('api')->user())->denies('update-utility', $utility)) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $file = $request->file('image');
        $fileName = Storage::disk('local')->put('tmp/images', $file);
    
        if (!Storage::disk('local')->exists($fileName)) {
            return Response::generateResponse(HttpStatusCode::INTERNAL_SERVER_ERROR, '', []);
        }

        ProcessUpdateUtility::dispatch(Auth::guard('api')->user()->id, $id, $request->except('image'), $fileName, Helper::getClientIps(), Helper::getClientAgent());
        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }
}
