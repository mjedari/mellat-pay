<?php

namespace Mjedari\MellatPay;

use Illuminate\Support\ServiceProvider;
use Mjedari\MellatPay\Commands\MellatPayCommand;

class MellatPayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mellat-pay');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mellat-pay');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('mellatpay.php'),
            ], 'mellat-pay-config');

            // Publishing the views.
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/mellat-pay'),
            ], 'mellat-pay-views');

            // Publishing the translation files.
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/mellat-pay'),
            ], 'mellat-pay-lang');

            // Publishing the migration file.
            $this->publishes([
                __DIR__.'/../database/migrations' => resource_path('database/migrations/mellat-pay'),
            ], 'mellat-pay-migrations');

            // Registering package commands.
            $this->commands([
                MellatPayCommand::class
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'mellatpay');

        // Register the main class to use with the facade
        $this->app->singleton('mellat-pay', function ($app) {
            $app->setLocale(config('mellatpay.local'));
            return new MellatPay();
        });
    }
}
