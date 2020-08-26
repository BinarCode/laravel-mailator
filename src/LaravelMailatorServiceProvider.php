<?php

namespace Binarcode\LaravelMailator;

use Binarcode\LaravelMailator\Models\MailatorLog;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\ServiceProvider;

class LaravelMailatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if (config('mailator.scheduler.model')) {
            $this->app->bind(MailatorSchedule::class, config('mailator.model'));
        }

        if (config('mailator.log_model')) {
            $this->app->bind(MailatorLog::class, config('mailator.log_model'));
        }

        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-mailator');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-mailator');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/mailator.php' => config_path('mailator.php'),
            ], 'mailator-config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-mailator'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-mailator'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-mailator'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/mailator.php', 'mailator');

        // Register the main class to use with the facade
        $this->app->singleton('mailator', function () {
            return new LaravelMailator();
        });
    }
}
