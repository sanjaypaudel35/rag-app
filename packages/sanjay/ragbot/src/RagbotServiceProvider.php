<?php

namespace Sanjay\Ragbot;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\LoginResponse as FortifyLoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse as FortifyLogoutResponse;
use Laravel\Fortify\Contracts\RegisterResponse as FortifyRegisterResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Responses\LoginResponse as DefaultLoginResponse;
use Laravel\Fortify\Http\Responses\LogoutResponse as DefaultLogoutResponse;
use Laravel\Fortify\Http\Responses\RegisterResponse as DefaultRegisterResponse;
use Livewire\Livewire;
use Sanjay\Ragbot\Actions\Fortify\CreateNewUser as TenantCreateNewUser;
use Sanjay\Ragbot\Actions\Fortify\LoginResponse as TenantLoginResponse;
use Sanjay\Ragbot\Actions\Fortify\LogoutResponse as TenantLogoutResponse;
use Sanjay\Ragbot\Actions\Fortify\RegisterResponse as TenantRegisterResponse;
use Sanjay\Ragbot\Contracts\Repositories\ChatbotRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\ChunkRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\ConversationRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\DocumentRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\EmbeddingRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\MessageRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\ProjectRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\ProjectSettingRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\UserRepositoryInterface;
use Sanjay\Ragbot\Contracts\Services\EmbeddingInterface;
use Sanjay\Ragbot\Contracts\Services\KeywordSearchInterface;
use Sanjay\Ragbot\Contracts\Services\LlmInterface;
use Sanjay\Ragbot\Contracts\Services\PromptBuilderServiceInterface;
use Sanjay\Ragbot\Contracts\Services\RetrievalServiceInterface;
use Sanjay\Ragbot\Contracts\Services\VectorStoreInterface;
use Sanjay\Ragbot\Http\Controllers\Auth\LoginController;
use Sanjay\Ragbot\Http\Controllers\Auth\RegisterController;
use Sanjay\Ragbot\Http\Controllers\Auth\Tenant\LoginController as TenantLoginController;
use Sanjay\Ragbot\Http\Controllers\Auth\Tenant\RegisterController as TenantRegisterController;
use Sanjay\Ragbot\Http\Middleware\EnforceChatbotRateLimits;
use Sanjay\Ragbot\Http\Middleware\HandleChatbotCors;
use Sanjay\Ragbot\Http\Middleware\RedirectIfNotRagbotAuthenticated;
use Sanjay\Ragbot\Http\Middleware\ResolveProjectFromApiKey;
use Sanjay\Ragbot\Http\Middleware\ResolveProjectFromSlug;
use Sanjay\Ragbot\Http\Middleware\SetRagbotAuthGuard;
use Sanjay\Ragbot\Livewire\Dashboard;
use Sanjay\Ragbot\Livewire\Tenant\Billing;
use Sanjay\Ragbot\Livewire\Tenant\ChatbotManager;
use Sanjay\Ragbot\Livewire\Tenant\DeveloperExtension;
use Sanjay\Ragbot\Livewire\Tenant\DocumentManager;
use Sanjay\Ragbot\Livewire\Tenant\IntegrationGuide;
use Sanjay\Ragbot\Livewire\Tenant\SettingsManager;
use Sanjay\Ragbot\Models\RagbotUser;
use Sanjay\Ragbot\Repositories\ChatbotRepository;
use Sanjay\Ragbot\Repositories\ChunkRepository;
use Sanjay\Ragbot\Repositories\ConversationRepository;
use Sanjay\Ragbot\Repositories\DocumentRepository;
use Sanjay\Ragbot\Repositories\EmbeddingRepository;
use Sanjay\Ragbot\Repositories\MessageRepository;
use Sanjay\Ragbot\Repositories\ProjectRepository;
use Sanjay\Ragbot\Repositories\ProjectSettingRepository;
use Sanjay\Ragbot\Repositories\UserRepository;
use Sanjay\Ragbot\Services\Auth\LoginService;
use Sanjay\Ragbot\Services\Auth\RegisterService;
use Sanjay\Ragbot\Services\Auth\Tenant\LoginService as TenantLoginService;
use Sanjay\Ragbot\Services\Auth\Tenant\RegisterService as TenantRegisterService;
use Sanjay\Ragbot\Services\Tenant\ChatService;
use Sanjay\Ragbot\Services\Tenant\CostCalculator;
use Sanjay\Ragbot\Services\Tenant\DocumentService;
use Sanjay\Ragbot\Services\Tenant\EmbeddingManager;
use Sanjay\Ragbot\Services\Tenant\LlmManager;
use Sanjay\Ragbot\Services\Tenant\PromptBuilderService;
use Sanjay\Ragbot\Services\Tenant\RetrievalService;
use Sanjay\Ragbot\Services\Tenant\VectorStoreManager;
use Sanjay\Ragbot\Services\Tenant\WidgetService;

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
        $this->app->bind(ChatbotRepositoryInterface::class, ChatbotRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(ChunkRepositoryInterface::class, ChunkRepository::class);
        $this->app->bind(EmbeddingRepositoryInterface::class, EmbeddingRepository::class);
        $this->app->bind(ConversationRepositoryInterface::class, ConversationRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(ProjectSettingRepositoryInterface::class, ProjectSettingRepository::class);

        $this->app->singleton(LlmInterface::class, LlmManager::class);
        $this->app->singleton(EmbeddingInterface::class, EmbeddingManager::class);
        $this->app->singleton(VectorStoreInterface::class, VectorStoreManager::class);
        $this->app->singleton(KeywordSearchInterface::class, VectorStoreManager::class);

        $this->app->singleton(RetrievalServiceInterface::class, RetrievalService::class);
        $this->app->singleton(PromptBuilderServiceInterface::class, PromptBuilderService::class);
        $this->app->singleton(ChatService::class);
        $this->app->singleton(CostCalculator::class);

        $this->app->singleton(DocumentService::class);
        $this->app->singleton(WidgetService::class);
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
        $this->configureContextualBindings();

        // Set default Fortify home to platform dashboard
        config(['fortify.home' => '/ragbot/dashboard']);

        RedirectIfAuthenticated::redirectUsing(function ($request) {
            if (Auth::guard('ragbot')->check() && app()->bound('ragbot.project')) {
                return route('ragbot.dashboard', ['project_slug' => app('ragbot.project')->slug]);
            }

            if (Auth::guard(config('auth.defaults.guard'))->check()) {
                return route('ragbot.platform.dashboard');
            }

            return null;
        });
    }

    /**
     * Register the Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        Livewire::component('ragbot.dashboard', Dashboard::class);
        Livewire::component('ragbot.document-manager', DocumentManager::class);
        Livewire::component('ragbot.settings-manager', SettingsManager::class);
        Livewire::component('ragbot.chatbot-manager', ChatbotManager::class);
        Livewire::component('ragbot.team-manager', TeamManager::class);
        Livewire::component('ragbot.integration-guide', IntegrationGuide::class);
        Livewire::component('ragbot.developer-extension', DeveloperExtension::class);
        Livewire::component('ragbot.processing-queue', ProcessingQueue::class);
        Livewire::component('ragbot.profile-settings', ProfileSettings::class);
        Livewire::component('ragbot.billing', Billing::class);
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

        config(['auth.passwords.ragbot_users' => [
            'provider' => 'ragbot_users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ]]);
    }

    /**
     * Configure contextual bindings for Fortify contracts and Services.
     */
    protected function configureContextualBindings(): void
    {
        // CreatesNewUsers injection into Services
        $this->app->when(RegisterService::class)
            ->needs(CreatesNewUsers::class)
            ->give(CreateNewUser::class);

        $this->app->when(TenantRegisterService::class)
            ->needs(CreatesNewUsers::class)
            ->give(TenantCreateNewUser::class);

        // RegisterResponse
        $this->app->when(RegisterController::class)
            ->needs(FortifyRegisterResponse::class)
            ->give(DefaultRegisterResponse::class);

        $this->app->when(TenantRegisterController::class)
            ->needs(FortifyRegisterResponse::class)
            ->give(TenantRegisterResponse::class);

        // LoginResponse
        $this->app->when(LoginController::class)
            ->needs(FortifyLoginResponse::class)
            ->give(DefaultLoginResponse::class);

        $this->app->when(TenantLoginController::class)
            ->needs(FortifyLoginResponse::class)
            ->give(TenantLoginResponse::class);

        // LogoutResponse
        $this->app->when(LoginController::class)
            ->needs(FortifyLogoutResponse::class)
            ->give(DefaultLogoutResponse::class);

        $this->app->when(TenantLoginController::class)
            ->needs(FortifyLogoutResponse::class)
            ->give(TenantLogoutResponse::class);

        // StatefulGuard - Platform (resolves to default guard)
        foreach ([RegisterController::class, LoginController::class, LoginService::class] as $class) {
            $this->app->when($class)
                ->needs(StatefulGuard::class)
                ->give(function () {
                    return Auth::guard(config('auth.defaults.guard'));
                });
        }

        // StatefulGuard - Tenant (resolves to 'ragbot' guard)
        foreach ([TenantRegisterController::class, TenantLoginController::class, TenantLoginService::class] as $class) {
            $this->app->when($class)
                ->needs(StatefulGuard::class)
                ->give(function () {
                    return Auth::guard('ragbot');
                });
        }
    }

    /**
     * Register the package middleware.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', ResolveProjectFromSlug::class);

        $router->aliasMiddleware('ragbot.auth', ResolveProjectFromApiKey::class);
        $router->aliasMiddleware('ragbot.cors', HandleChatbotCors::class);
        $router->aliasMiddleware('ragbot.ratelimit', EnforceChatbotRateLimits::class);
        $router->aliasMiddleware('ragbot.project', ResolveProjectFromSlug::class);
        $router->aliasMiddleware('ragbot.guard', SetRagbotAuthGuard::class);
        $router->aliasMiddleware('ragbot.authenticated', RedirectIfNotRagbotAuthenticated::class);
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        Route::group([
            'as' => 'ragbot.',
            'prefix' => config('ragbot.prefix', 'ragbot'),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        Route::group($this->apiRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
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
            'middleware' => config('ragbot.api_middleware', ['api']),
        ];
    }
}
