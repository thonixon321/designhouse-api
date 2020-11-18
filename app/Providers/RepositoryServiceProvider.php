<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\{
    IDesign,
    IUser,
    IComment
};
use App\Repositories\Eloquent\{
    DesignRepository,
    UserRepository,
    CommentRepository
};
//links the contracts interfaces to the concrete Repository methods in the Eloquent folder under Repositories
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //bind the interface with the repository
        //Have to register this service provider in the config/app in order to use $this->app
        $this->app->bind(IDesign::class, DesignRepository::class);
        $this->app->bind(IUser::class, UserRepository::class);
        $this->app->bind(IComment::class, CommentRepository::class);
    }
}
