<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;
use Socialite;
use Auth;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;

class SocialAccountController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Response::generateResponse(HttpStatusCode::OK, '', [
            'provider' => $provider,
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ]);

    }

    public function handleProviderCallback(SocialAccountService $service, $provider)
    {
        $user = $service->createOrGetUser(Socialite::with($provider)->stateless());
        if ($user) {
            $authToken = $user->createToken('MyApp');
            return redirect()->to('http://localhost:3000/auth/process-login-social?token=' . $authToken->accessToken);
        }

    }

}
