<?php

namespace Keenops\LaravelSms;

use Illuminate\Support\ServiceProvider;

class LaravelSmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {


        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('laravel-sms.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel-sms');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-sms', function () {
            return new LaravelSms;
        });
    }
}
