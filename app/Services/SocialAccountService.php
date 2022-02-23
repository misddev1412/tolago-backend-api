<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialAccount;
use Laravel\Socialite\Contracts\Provider;
use App\Helpers\Helper;
use Str;
use App\Jobs\Auth\ProcessCreateUserFromSocial;

class SocialAccountService
{
    public function createOrGetUser($provider)
    {
        $providerUser = $provider->user();
        $providerName = class_basename($provider);
        $account = SocialAccount::whereProvider($providerName)
            ->whereProviderUserId($providerUser->getId())
            ->first();

        if ($account) {
            return $account->user;
        } else {
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $providerName
            ]);

            $user = User::whereEmail($providerUser->getEmail())->first();

            if (!$user) {
                $dataCreate = [
                    'email' => $providerUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'first_name' => $providerUser->getName(),
                    'last_name' => '',
                    'is_random_password' => 1
                    // 'avatar' => $providerUser->getAvatar(),
                ];

                if ($providerName == 'FacebookProvider') {
                    $dataCreate['facebook_id'] = $providerUser->getId();
                } else if ($providerName == 'GoogleProvider') {
                    $dataCreate['google_id'] = $providerUser->getId();
                }
                
                $user = User::create($dataCreate);
                ProcessCreateUserFromSocial::dispatch($providerUser->getAvatar(), $user->id, Helper::getClientIps(), Helper::getClientAgent());
            } else {
                $dataUpdate = [];
                if ($providerName == 'FacebookProvider') {
                    $dataUpdate['facebook_id'] = $providerUser->getId();
                } else if ($providerName == 'GoogleProvider') {
                    $dataUpdate['google_id'] = $providerUser->getId();
                }
                $user->update($dataUpdate);
            }

            $account->user()->associate($user);
            $account->save();

            return $user;
        }
    }
}