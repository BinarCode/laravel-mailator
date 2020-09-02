<?php

namespace Binarcode\LaravelMailator;

use Binarcode\LaravelMailator\Models\MailatorLog;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Models\MailTemplate;
use Binarcode\LaravelMailator\Models\MailTemplatePlaceholder;
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

        if (config('mailator.templates.template_model')) {
            $this->app->bind(MailTemplate::class, config('mailator.templates.template_model'));
        }

        if (config('mailator.templates.placeholder_model')) {
            $this->app->bind(MailTemplatePlaceholder::class, config('mailator.templates.placeholder_model'));
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

            if (! class_exists('CreateMailatorTables')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_mailator_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_mailator_tables.php'),
                ], 'mailator-migrations');
            }
            // Publishing the views.
            $this->publishes([
                __DIR__.'/../resources/views/publish' => resource_path('views/vendor/laravel-mailator'),
            ], 'mailator-views');

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
