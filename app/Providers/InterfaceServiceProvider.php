<?php

namespace App\Providers;

use App\Interfaces\Eloquent\ICarBrandModelService;
use App\Interfaces\Eloquent\ICarBrandService;
use App\Interfaces\Eloquent\IUserService;
use App\Interfaces\PricePrediction\IPricePredictionService;
use App\Services\Eloquent\CarBrandModelService;
use App\Services\Eloquent\CarBrandService;
use App\Services\Eloquent\UserService;
use App\Services\PricePrediction\PricePredictionService;
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
        $this->app->bind(IPricePredictionService::class, PricePredictionService::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(ICarBrandService::class, CarBrandService::class);
        $this->app->bind(ICarBrandModelService::class, CarBrandModelService::class);
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
