<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('authx.route_prefix', 'auth'),
    'middleware' => ['api'],
], function () {
    Route::get('ping', function () {
        return response()->json([
            'ok' => true,
            'package' => 'rainwaves/lara-auth-suite',
            'version' => '0.0.1-dev',
        ]);
    });
});
