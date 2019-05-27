<?php

namespace Ycan\ApiAuth;

use Illuminate\Support\ServiceProvider;

class ApiAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/../config/apiAuth.php' => config_path('apiAuth.php'),
        ], 'apiAuth');

        $this->commands([
            Command::class,
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        //
    }

}