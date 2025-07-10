<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\BinCollectionRepositoryInterface;
use App\Repositories\BinCollectionRepository;
use App\Repositories\WidgetRepositoryInterface;
use App\Repositories\WidgetRepository;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Models\BinCollection;
use App\Models\Widget;
use App\Models\User;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BinCollectionRepositoryInterface::class, function ($app) {
            return new BinCollectionRepository(new BinCollection());
        });

        $this->app->bind(WidgetRepositoryInterface::class, function ($app) {
            return new WidgetRepository(new Widget());
        });

        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            return new UserRepository(new User());
        });
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
