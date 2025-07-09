<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\BinCollectionRepositoryInterface;
use App\Repositories\BinCollectionRepository;
use App\Repositories\WidgetRepositoryInterface;
use App\Repositories\WidgetRepository;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\SmartHomePlatformRepositoryInterface;
use App\Repositories\SmartHomePlatformRepository;
use App\Repositories\SmartDeviceRepositoryInterface;
use App\Repositories\SmartDeviceRepository;
use App\Repositories\SmartDeviceWidgetConfigRepositoryInterface;
use App\Repositories\SmartDeviceWidgetConfigRepository;
use App\Models\BinCollection;
use App\Models\Widget;
use App\Models\User;
use App\Models\SmartHomePlatform;
use App\Models\SmartDevice;
use App\Models\SmartDeviceWidgetConfig;

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

        $this->app->bind(SmartHomePlatformRepositoryInterface::class, function ($app) {
            return new SmartHomePlatformRepository(new SmartHomePlatform());
        });

        $this->app->bind(SmartDeviceRepositoryInterface::class, function ($app) {
            return new SmartDeviceRepository(new SmartDevice());
        });

        $this->app->bind(SmartDeviceWidgetConfigRepositoryInterface::class, function ($app) {
            return new SmartDeviceWidgetConfigRepository(new SmartDeviceWidgetConfig());
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
