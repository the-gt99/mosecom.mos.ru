<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Currency as CurrencyServices;
use App\Services\Identifier as IdentifierService;
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
