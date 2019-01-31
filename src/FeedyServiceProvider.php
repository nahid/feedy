<?php

namespace Nahid\Feedy;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class FeedyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->setupConfig();
        $this->setupMigrations();
        $blade = new Blades();
        $blade->runCompiles();
    }
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->registerFeedy();

    }
    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/feedy.php.php');
        // Check if the application is a Laravel OR Lumen instance to properly merge the configuration file.
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('permit.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('feedy');
        }
        $this->mergeConfigFrom($source, 'feedy');
    }
    /**
     * Publish migrations files.
     */
    protected function setupMigrations()
    {
        $this->publishes([
            realpath(__DIR__.'/../database/migrations/') => database_path('migrations'),
        ], 'migrations');
    }
    /**
     * Register Talk class.
     */
    protected function registerFeedy()
    {
        $this->app->singleton('feedy', function (Container $app) {
            return new Feed(
                $app['config']
            );
        });

        $this->app->alias('feedy', Feed::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'feedy',
        ];
    }
}
