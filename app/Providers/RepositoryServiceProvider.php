<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //register post repository
        $this->app->bind(
            'App\Repositories\Post\PostRepositoryInterface',
            'App\Repositories\Post\PostRepository'
        );
        //register repository roles
        $this->app->bind(
            'App\Repositories\Role\RoleRepositoryInterface',
            'App\Repositories\Role\RoleRepository'
        );
        //register repository user
        $this->app->bind(
            'App\Repositories\User\UserRepositoryInterface',
            'App\Repositories\User\UserRepository'
        );

        //register repository userFriend
        $this->app->bind(
            'App\Repositories\UserFriend\UserFriendRepositoryInterface',
            'App\Repositories\UserFriend\UserFriendRepository'
        );

        //register repository hotel
        $this->app->bind(
            'App\Repositories\Hotel\HotelRepositoryInterface',
            'App\Repositories\Hotel\HotelRepository'
        );

        //register repository room
        $this->app->bind(
            'App\Repositories\Room\RoomRepositoryInterface',
            'App\Repositories\Room\RoomRepository'
        );

        //register repository utilities
        $this->app->bind(
            'App\Repositories\Utility\UtilityRepositoryInterface',
            'App\Repositories\Utility\UtilityRepository'
        );

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
