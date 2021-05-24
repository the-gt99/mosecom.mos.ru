<?php

namespace App\Providers;

use App\Repositories\AirCms\AirCmsRepository;
use App\Services\AirCms\AirCmsAdapter;
use App\Services\AirCms\AirCmsService;
use App\Services\Grabber;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Grabber::class, function ($app) {
            return new Grabber();
        });
        $this->app->bind(AirCmsService::class, function ($app) {
            return new AirCmsService(new AirCmsRepository);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
    }
}
