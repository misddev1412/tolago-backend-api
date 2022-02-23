<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport; 
use Carbon\Carbon;
use App\Policies\PostPolicy;
use App\Policies\HotelPolicy;
use App\Policies\RoomPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->registerPostPolicies();
        $this->registerHotelPolicies();
        $this->registerRoomPolicies();

        if (! $this->app->routesAreCached()) {
            Passport::routes();
            Passport::tokensExpireIn(now()->addDays(15));
            Passport::refreshTokensExpireIn(now()->addDays(30));
            Passport::personalAccessTokensExpireIn(now()->addDays(1));
            // Passport::hashClientSecrets();
        }
        //
    }

    private function registerPostPolicies() {
        Gate::define('create-post', [PostPolicy::class, 'create']);
        Gate::define('update-post', [PostPolicy::class, 'update']);
        Gate::define('view-post', [PostPolicy::class, 'view']);
        Gate::define('view-all-post', [PostPolicy::class, 'viewAny']);
        Gate::define('delete-post', [PostPolicy::class, 'delete']);
    }

    private function registerHotelPolicies() {
        Gate::define('create-hotel', [HotelPolicy::class, 'create']);
        Gate::define('update-hotel', [HotelPolicy::class, 'update']);
        Gate::define('view-hotel', [HotelPolicy::class, 'view']);
        Gate::define('view-all-hotel', [HotelPolicy::class, 'viewAny']);
        Gate::define('delete-hotel', [HotelPolicy::class, 'delete']);
    }

    private function registerRoomPolicies() {
        Gate::define('create-room', [RoomPolicy::class, 'create']);
        Gate::define('update-room', [RoomPolicy::class, 'update']);
        Gate::define('view-room', [RoomPolicy::class, 'view']);
        Gate::define('view-all-room', [RoomPolicy::class, 'viewAny']);
        Gate::define('delete-room', [RoomPolicy::class, 'delete']);
    }
}
