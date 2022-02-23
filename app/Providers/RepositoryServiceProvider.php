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

        //register repository hotel
        $this->app->bind(
            'App\Repositories\Hotel\HotelRepositoryInterface',
            'App\Repositories\Hotel\HotelRepository'
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
