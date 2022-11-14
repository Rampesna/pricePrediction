<?php

namespace App\Providers;

use App\Interfaces\IAutoscoutService;
use App\Interfaces\IMobileDeService;
use App\Services\Autoscout\AutoscoutService;
use App\Services\MobileDe\MobileDeService;
use Illuminate\Support\ServiceProvider;

class InterfaceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IAutoscoutService::class, AutoscoutService::class);
        $this->app->bind(IMobileDeService::class, MobileDeService::class);
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
