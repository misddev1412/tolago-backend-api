<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Enum\HttpStatusCode;
use Auth;
use Illuminate\Support\Facades\Broadcast;
use App\Jobs\Auth\ProcessSignup;
use App\Jobs\User\ProcessAdminUpdateUser;
use App\Helpers\Helper;
use App\Helpers\Response;
use Storage;

class UserController extends Controller
{
    //construct user repository
    protected $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    //index userRepository
    public function index(Request $request)
    {
        return $this->userRepository->index($request);
    }

    //create user
    public function store(SignupRequest $request)
    {
        $signupData = [
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ];

        ProcessSignup::dispatch($signupData, $request->except('password', 'retype_password'), $this->userRepository, Helper::getClientIps(), Helper::getClientAgent());
        return Response::generateResponse(HttpStatusCode::CREATED, '', []);
    }

    //update user
    public function update(UpdateUserRequest $request, $userId)
    {
        $fileName = '';
        if ($request->file('avatar')) {
            $file = $request->file('avatar');
            $fileName = Storage::disk('local')->put('tmp/images', $file);
        }
        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];


        ProcessAdminUpdateUser::dispatch($userId, $updateData, $fileName, Auth::guard('api')->user()->id, Helper::getClientIps(), Helper::getClientAgent());



        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }

    public function newest()
    {
        return Response::generateResponse(HttpStatusCode::OK, '', $this->userRepository->newest(5));
    }
}
