<?php

namespace Sanjay\Ragbot;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Sanjay\Ragbot\Contracts\Repositories\ProjectRepositoryInterface;
use Sanjay\Ragbot\Http\Middleware\ResolveProjectFromApiKey;
use Sanjay\Ragbot\Repositories\ProjectRepository;

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
            __DIR__."/../config/ragbot.php", "ragbot"
        );

        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(\Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface::class, \Sanjay\Ragbot\Repositories\DocumentRepository::class);
        $this->app->bind(\Sanjay\Ragbot\Contracts\Repositories\ChunkRepositoryInterface::class, \Sanjay\Ragbot\Repositories\ChunkRepository::class);
        $this->app->bind(\Sanjay\Ragbot\Contracts\Repositories\EmbeddingRepositoryInterface::class, \Sanjay\Ragbot\Repositories\EmbeddingRepository::class);
        $this->app->bind(\Sanjay\Ragbot\Contracts\Repositories\ConversationRepositoryInterface::class, \Sanjay\Ragbot\Repositories\ConversationRepository::class);
        $this->app->bind(\Sanjay\Ragbot\Contracts\Repositories\MessageRepositoryInterface::class, \Sanjay\Ragbot\Repositories\MessageRepository::class);
        $this->app->bind(\Sanjay\Ragbot\Contracts\Repositories\ProjectSettingRepositoryInterface::class, \Sanjay\Ragbot\Repositories\ProjectSettingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__."/../database/migrations");

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__."/../config/ragbot.php" => config_path("ragbot.php"),
            ], "ragbot-config");

            $this->publishes([
                __DIR__."/../database/migrations/" => database_path("migrations"),
            ], "ragbot-migrations");
        }

        $this->registerMiddleware();
    }

    /**
     * Register the package middleware.
     */
    protected function registerMiddleware(): void
    {
        $this->app["router"]->aliasMiddleware("ragbot.auth", ResolveProjectFromApiKey::class);
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__."/../routes/web.php");
        });

        Route::group($this->apiRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__."/../routes/api.php");
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
            "prefix" => config("ragbot.prefix", "ragbot"),
            "middleware" => config("ragbot.middleware", ["web"]),
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
            "prefix" => config("ragbot.prefix", "ragbot") . "/api",
            "middleware" => config("ragbot.api_middleware", ["api"]),
        ];
    }
}
