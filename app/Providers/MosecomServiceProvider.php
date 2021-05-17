<?php

namespace App\Providers;

use App\Services\Mosecom\MosecomService;
use Illuminate\Support\ServiceProvider;
use App\Services\Mosecom\MosecomParser;

class MosecomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MosecomParser::class, function ($app) {
            return new MosecomParser();
        });

        $this->app->singleton(MosecomService::class, function ($app) {
            $parser = $app->make(MosecomParser::class);
            return new MosecomService($parser);
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
