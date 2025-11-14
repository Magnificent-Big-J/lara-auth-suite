<?php

namespace Rainwaves\LaraAuthSuite;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Rainwaves\LaraAuthSuite\Contracts\AuthService;
use Rainwaves\LaraAuthSuite\Contracts\PasswordResetService;
use Rainwaves\LaraAuthSuite\Contracts\PermissionSyncService;
use Rainwaves\LaraAuthSuite\Contracts\RegistrationService;
use Rainwaves\LaraAuthSuite\Contracts\SessionAuthService;
use Rainwaves\LaraAuthSuite\Http\Middleware\EnsureTwoFactorVerified;
use Rainwaves\LaraAuthSuite\Services\Auth\AuthServiceImpl;
use Rainwaves\LaraAuthSuite\Services\Auth\PasswordResetServiceImpl;
use Rainwaves\LaraAuthSuite\Services\Auth\PermissionSync\SpatiePermissionSyncService;
use Rainwaves\LaraAuthSuite\Services\Auth\RegistrationServiceImpl;
use Rainwaves\LaraAuthSuite\Services\Auth\SessionAuthServiceImpl;
use Rainwaves\LaraAuthSuite\Services\TwoFactor\TwoFactorAuthService;
use Rainwaves\LaraAuthSuite\Token\Contracts\TokenManager;
use Rainwaves\LaraAuthSuite\Token\Sanctum\SanctumTokenManager;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\ITwoFactorAuth;

class LaraAuthSuiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/Config/authx.php', 'authx');

        // Token manager DI
        $this->app->bind(
            TokenManager::class,
            SanctumTokenManager::class
        );

        // Auth service DI (pull user model from config)
        $this->app->bind(
            AuthService::class,
            function ($app) {
                $userModel = $app['config']->get('authx.user_model', User::class);

                return new AuthServiceImpl($userModel);
            }
        );
        $this->app->bind(
            PasswordResetService::class,
            PasswordResetServiceImpl::class
        );

        $this->app->bind(
            SessionAuthService::class,
            fn ($app) => new SessionAuthServiceImpl(
                $app['config']->get('authx.user_model', User::class)
            )
        );

        $this->app->singleton(
            ITwoFactorAuth::class,
            TwoFactorAuthService::class
        );

        $this->app->singleton(
            PermissionSyncService::class,
            fn ($app) => new SpatiePermissionSyncService(
                (bool) $app['config']->get('authx.permissions.enabled', true)
            )
        );

        $this->app->singleton(
            RegistrationService::class,
            fn ($app) => new RegistrationServiceImpl(
                $app->make(PermissionSyncService::class),
                $app['config']->get('authx.user_model', User::class)
            )
        );

    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/Config/authx.php' => config_path('authx.php'),
        ], 'authx-config');

        $this->publishes([
            __DIR__.'/Database/migrations' => database_path('migrations'),
        ], 'authx-migrations');

        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');
        $this->app['router']->aliasMiddleware('2fa.enforced', EnsureTwoFactorVerified::class);
    }

}
