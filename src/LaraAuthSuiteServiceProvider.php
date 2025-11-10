<?php

namespace Rainwaves\LaraAuthSuite;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Rainwaves\LaraAuthSuite\Contracts\AuthService;
use Rainwaves\LaraAuthSuite\Contracts\PasswordResetService;
use Rainwaves\LaraAuthSuite\Contracts\SessionAuthService;
use Rainwaves\LaraAuthSuite\Services\Auth\AuthServiceImpl;
use Rainwaves\LaraAuthSuite\Services\Auth\PasswordResetServiceImpl;
use Rainwaves\LaraAuthSuite\Services\Auth\SessionAuthServiceImpl;
use Rainwaves\LaraAuthSuite\Token\Contracts\TokenManager;
use Rainwaves\LaraAuthSuite\Token\Sanctum\SanctumTokenManager;

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
            fn($app) => new SessionAuthServiceImpl(
                $app['config']->get('authx.user_model', User::class)
            )
        );


    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/Config/authx.php' => config_path('authx.php'),
        ], 'authx-config');

        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');
    }

}
