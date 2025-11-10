<?php

namespace Rainwaves\LaraAuthSuite;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LaraAuthSuiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/Config/authx.php', 'authx');

        // Token manager DI
        $this->app->bind(
            \Rainwaves\LaraAuthSuite\Token\Contracts\TokenManager::class,
            \Rainwaves\LaraAuthSuite\Token\Sanctum\SanctumTokenManager::class
        );

        // Auth service DI (pull user model from config)
        $this->app->bind(
            \Rainwaves\LaraAuthSuite\Contracts\AuthService::class,
            function ($app) {
                $userModel = $app['config']->get('authx.user_model', \App\Models\User::class);
                return new \Rainwaves\LaraAuthSuite\Services\Auth\AuthServiceImpl($userModel);
            }
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
