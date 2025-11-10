<?php

namespace Rainwaves\LaraAuthSuite;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LaraAuthSuiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Package config
        $this->mergeConfigFrom(__DIR__.'/Config/authx.php', 'authx');
    }

    public function boot(): void
    {
        // Publishable config
        $this->publishes([
            __DIR__.'/Config/authx.php' => config_path('authx.php'),
        ], 'authx-config');

        // Routes (API)
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');

        // (Optional) middleware aliases can be registered here later
        // app('router')->aliasMiddleware('authsuite', \Rainwaves\LaraAuthSuite\Http\Middleware\AuthSuite::class);
    }
}
