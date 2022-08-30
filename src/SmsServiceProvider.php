<?php

namespace Keenops\Sms;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
     /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes(
            [__DIR__.'/../config/laravel-beem-sms.php' => config_path('laravel-beem-sms.php')],
            ['laravel-beem-sms']
        );
    }

     /**
     * Register the application services.
     */
    public function register()
    {
        /**
         * Automatically apply the package configuration
         */
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-beem-sms.php', 'laravel-beem-sms');

        /**
         * Bind facade to serviceProvider
         */
        $this->app->bind('sms', function($app) {
            return new Sms();
        });
        
        /**
         * Register the main class to use with the facade
         */
        $this->app->singleton(Sms::class, function(){
            return new Sms();
        }); 
    }
}