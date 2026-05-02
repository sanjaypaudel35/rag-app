<?php

namespace Sanjay\Ragbot;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Livewire\Livewire;
use Sanjay\Ragbot\Actions\Fortify\CreateNewUser;
use Sanjay\Ragbot\Actions\Fortify\LoginResponse;
use Sanjay\Ragbot\Actions\Fortify\LogoutResponse;
use Sanjay\Ragbot\Actions\Fortify\RegisterResponse;
use Sanjay\Ragbot\Contracts\Repositories\ChunkRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\ConversationRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\EmbeddingRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\MessageRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\ProjectRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\ProjectSettingRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\UserRepositoryInterface;
use Sanjay\Ragbot\Http\Middleware\RedirectIfNotRagbotAuthenticated;
use Sanjay\Ragbot\Http\Middleware\ResolveProjectFromApiKey;
use Sanjay\Ragbot\Http\Middleware\ResolveProjectFromSlug;
use Sanjay\Ragbot\Http\Middleware\SetRagbotAuthGuard;
use Sanjay\Ragbot\Livewire\Dashboard;
use Sanjay\Ragbot\Models\RagbotUser;
use Sanjay\Ragbot\Repositories\ChunkRepository;
use Sanjay\Ragbot\Repositories\ConversationRepository;
use Sanjay\Ragbot\Repositories\DocumentRepository;
use Sanjay\Ragbot\Repositories\EmbeddingRepository;
use Sanjay\Ragbot\Repositories\MessageRepository;
use Sanjay\Ragbot\Repositories\ProjectRepository;
use Sanjay\Ragbot\Repositories\ProjectSettingRepository;
use Sanjay\Ragbot\Repositories\UserRepository;

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

        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(ChunkRepositoryInterface::class, ChunkRepository::class);
        $this->app->bind(EmbeddingRepositoryInterface::class, EmbeddingRepository::class);
        $this->app->bind(ConversationRepositoryInterface::class, ConversationRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(ProjectSettingRepositoryInterface::class, ProjectSettingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerLivewireComponents();
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'ragbot');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ragbot.php' => config_path('ragbot.php'),
            ], 'ragbot-config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'ragbot-migrations');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/ragbot'),
            ], 'ragbot-views');
        }

        $this->registerMiddleware();
        $this->configureGuard();
        $this->configureFortify();

        RedirectIfAuthenticated::redirectUsing(function ($request) {
            if (Auth::guard('ragbot')->check() && app()->bound('ragbot.project')) {
                return route('ragbot.dashboard', ['project_slug' => app('ragbot.project')->slug]);
            }

            return null;
        });
    }

    /**
     * Register the Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        // Livewire::component('ragbot::dashboard', Dashboard::class);
    }

    /**
     * Configure the authentication guard.
     */
    protected function configureGuard(): void
    {
        config(['auth.guards.ragbot' => [
            'driver' => 'session',
            'provider' => 'ragbot_users',
        ]]);

        config(['auth.providers.ragbot_users' => [
            'driver' => 'eloquent',
            'model' => RagbotUser::class,
        ]]);
    }

    /**
     * Configure Laravel Fortify.
     */
    protected function configureFortify(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        $this->app->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            LoginResponse::class
        );

        $this->app->singleton(
            \Laravel\Fortify\Contracts\RegisterResponse::class,
            RegisterResponse::class
        );

        $this->app->singleton(
            \Laravel\Fortify\Contracts\LogoutResponse::class,
            LogoutResponse::class
        );

        Fortify::loginView(function () {
            return view('ragbot::auth.login');
        });

        Fortify::registerView(function () {
            return view('ragbot::auth.register');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('ragbot::auth.forgot-password');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('ragbot::auth.reset-password', ['request' => $request]);
        });
    }

    /**
     * Register the package middleware.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('ragbot.auth', ResolveProjectFromApiKey::class);
        $router->aliasMiddleware('ragbot.project', ResolveProjectFromSlug::class);
        $router->aliasMiddleware('ragbot.guard', SetRagbotAuthGuard::class);
        $router->aliasMiddleware('ragbot.authenticated', RedirectIfNotRagbotAuthenticated::class);
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
            'as' => 'ragbot.',
            'prefix' => config('ragbot.prefix', 'ragbot').'/{project_slug}',
            'middleware' => array_merge(config('ragbot.middleware', ['web']), [ResolveProjectFromSlug::class, SetRagbotAuthGuard::class]),
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
            'prefix' => config('ragbot.prefix', 'ragbot').'/api',
            'middleware' => array_merge(config('ragbot.api_middleware', ['api']), [ResolveProjectFromApiKey::class]),
        ];
    }
}
