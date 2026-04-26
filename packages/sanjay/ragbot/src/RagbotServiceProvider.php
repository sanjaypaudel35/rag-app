<?php

namespace Sanjay\Ragbot;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for the Sanjay\Ragbot package.
 */
class RagbotServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/ragbot.php', 'ragbot'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ragbot.php' => config_path('ragbot.php'),
            ], 'ragbot-config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'ragbot-migrations');
        }
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        Route::group($this->apiRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    /**
     * Get the web route group configuration.
     *
     * @return array<string, mixed>
     */
    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('ragbot.prefix', 'ragbot'),
            'middleware' => config('ragbot.middleware', ['web']),
        ];
    }

    /**
     * Get the API route group configuration.
     *
     * @return array<string, mixed>
     */
    protected function apiRouteConfiguration(): array
    {
        return [
            'prefix' => 'api/'.config('ragbot.prefix', 'ragbot'),
            'middleware' => config('ragbot.api_middleware', ['api']),
        ];
    }
}
