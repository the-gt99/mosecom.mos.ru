<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Currency as CurrencyServices;
use App\Services\Identifier as IdentifierService;
use App\Services\Mosecom\MosecomParserService;

class MosecomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MosecomParserService::class, function ($app) {
            return new MosecomParserService();
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
