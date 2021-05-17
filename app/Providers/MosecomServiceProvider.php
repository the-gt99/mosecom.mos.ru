<?php

namespace App\Providers;

use App\Services\Mosecom\MosecomService;
use Illuminate\Support\ServiceProvider;
use App\Services\Currency as CurrencyServices;
use App\Services\Identifier as IdentifierService;
use App\Services\Mosecom\MosecomParser;
use App\Services\Mosecom\CurlClient;

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
            return new MosecomService();
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
