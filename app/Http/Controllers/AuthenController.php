<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\Response;
use App\Helpers\Helper;
use App\Enum\HttpStatusCode;
use App\Http\Requests\Auth\SignupRequest;
use Auth;
use Illuminate\Support\Facades\Broadcast;
use App\Jobs\Auth\ProcessSignup;
use App\Jobs\Auth\ProcessSignin;

class AuthenController extends Controller
{
    // init authen variables
    protected $user;

    //construct with model user instance
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    //login function return generateResponse
    public function login(Request $request)
    {
        $user = $this->user->where('email', $request->email)->first();
        if ($user) {
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                ProcessSignin::dispatch($request->except('password'), false, Helper::getClientIps(), Helper::getClientAgent());
                return Response::generateResponse(HttpStatusCode::UNAUTHORIZED, '', '');
            }

            ProcessSignin::dispatch($request->except('password'), true, Helper::getClientIps(), Helper::getClientAgent());

            $auth = Auth::user();
            $authToken = $auth->createToken('MyApp');
            $dataResponse = [
                'id' => $auth->id,
                'name' => $auth->name,
                'token' => $authToken->accessToken,
                'expires_at' => $authToken->token->expires_at,
                'token_type' => 'Bearer',
            ];


            return Response::generateResponse(HttpStatusCode::OK, '', $dataResponse);
        } else {
            return Response::generateResponse(HttpStatusCode::UNAUTHORIZED, '', '');
        }
    }

    //bcrypt encoded password
    public function signup(SignupRequest $request)
    {
        $signupData = [
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'name' => $request->name,
        ];

        ProcessSignup::dispatch($signupData, $request->except('password', 'retype_password'), Helper::getClientIps(), Helper::getClientAgent());
        return Response::generateResponse(HttpStatusCode::CREATED, '', []);
    }

    //broadcastingAuth function
    public function broadcastingAuth(Request $request)
    {
        return Broadcast::auth($request);
    }

    //me function
    public function me()
    {
        return Response::generateResponse(HttpStatusCode::OK, '', Auth::guard('api')->user());
    }

    //logout function
    public function logout()
    {
        Auth::guard('api')->user()->token()->revoke();
        return Response::generateResponse(HttpStatusCode::OK, '', '');
    }

    //refresh function
    public function refresh()
    {
        $auth = Auth::guard('api')->user();
        $authToken = $auth->createToken('MyApp');

        $dataResponse = [
            'id' => $auth->id,
            'name' => $auth->name,
            'token' => $authToken->accessToken,
            'expires_at' => $authToken->token->expires_at,
            'token_type' => 'Bearer',
        ];

        return Response::generateResponse(HttpStatusCode::OK, '', $dataResponse);
    }

    //update profile function
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('api')->user();
        $user->name = $request->name;
        $user->save();
        return Response::generateResponse(HttpStatusCode::OK, '', $user);
    }
}
