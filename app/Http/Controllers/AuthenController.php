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
use App\Jobs\Auth\ProcessUpdateProfile;
use App\Jobs\Auth\ProcessEnableQrCode;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\EnableQrCodeRequest;
use Storage;
use App\Repositories\User\UserRepositoryInterface;

class AuthenController extends Controller
{
    // init authen variables
    protected $user;

    //construct with model user instance
    public function __construct(User $user, UserRepositoryInterface $userRepository)
    {
        $this->user = $user;
        $this->userRepository = $userRepository;
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
                'name' => $auth->fullname,
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
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ];

        ProcessSignup::dispatch($signupData, $request->except('password', 'retype_password'), $this->userRepository, Helper::getClientIps(), Helper::getClientAgent());
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
        $user = User::withAll()->find(Auth::guard('api')->user()->id);
        $user->update(['last_login' => now()]);
        return Response::generateResponse(HttpStatusCode::OK, '', $user);
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
            'name' => $auth->fullname,
            'token' => $authToken->accessToken,
            'expires_at' => $authToken->token->expires_at,
            'token_type' => 'Bearer',
        ];

        return Response::generateResponse(HttpStatusCode::OK, '', $dataResponse);
    }

    //update profile function
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return Response::generateResponse(HttpStatusCode::UNAUTHORIZED, '', '');
        }
        $fileName = '';
        if ($request->file('avatar')) {
            $file = $request->file('avatar');
            $fileName = Storage::disk('local')->put('tmp/images', $file);
        }

        $dataUpdate = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ];

        ProcessUpdateProfile::dispatch(Auth::guard('api')->user()->id, $dataUpdate, $fileName, Helper::getClientIps(), Helper::getClientAgent());
        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }

    public function createQrUrl()
    {
        $googleAuthenticator = new \PHPGangsta_GoogleAuthenticator();
        $secretCode = $googleAuthenticator->createSecret();
        $qrCodeUrl = $googleAuthenticator->getQRCodeGoogleUrl(
            Auth::guard('api')->user()->email, $secretCode, config("app.name")
        );
        User::where('id', Auth::guard('api')->user()->id)->update(['secret_code' => $secretCode]);
        return Response::generateResponse(HttpStatusCode::OK, '', [
            'qr_code_url' => $qrCodeUrl
        ]);
    }

    public function enableQrCode(EnableQrCodeRequest $request)
    {
        $googleAuthenticator = new \PHPGangsta_GoogleAuthenticator();
        $secretCode = Auth::guard('api')->user()->secret_code;
        $checkCode = $googleAuthenticator->verifyCode($secretCode, $request->otp, 0);
        if ($checkCode) {
            ProcessEnableQrCode::dispatch(Auth::guard('api')->user()->id, Helper::getClientIps(), Helper::getClientAgent());
            return Response::generateResponse(HttpStatusCode::OK, '', []);
        } else {
            return Response::generateResponse(HttpStatusCode::UNAUTHORIZED, __('OTP is not valid.'), '');
        }
    }
}
