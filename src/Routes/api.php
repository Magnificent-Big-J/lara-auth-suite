<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Rainwaves\LaraAuthSuite\Http\Controllers\AuthController;
use Rainwaves\LaraAuthSuite\Http\Controllers\PasswordResetController;
use Rainwaves\LaraAuthSuite\Http\Controllers\SessionAuthController;
use Rainwaves\LaraAuthSuite\Http\Controllers\TwoFactorManageController;

// ─────────────────────────────────────────────
// /auth/* — API stack (stateless): token auth + password reset
// ─────────────────────────────────────────────
Route::prefix(config('authx.route_prefix', 'auth'))
    ->middleware(['api'])
    ->group(function () {
        // Health check
        Route::get('ping', fn () => response()->json([
            'ok'      => true,
            'package' => 'rainwaves/lara-auth-suite',
            'version' => '0.0.1-dev',
        ]));

        // Password reset (email flow using Password broker)
        Route::post('password/forgot', [PasswordResetController::class, 'request']);
        Route::post('password/reset',  [PasswordResetController::class, 'reset']);

        // Token-based auth (Sanctum PAT)
        Route::post('login',  [AuthController::class, 'login']);
        Route::get('me',      [AuthController::class, 'me'])->middleware('auth:sanctum');
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

// ─────────────────────────────────────────────
// /auth/session/* — WEB stack (stateful): session/cookie auth for same-domain SPAs
// ─────────────────────────────────────────────
Route::prefix(config('authx.route_prefix', 'auth').'/session')
    ->middleware(['web'])
    ->group(function () {
        // CSRF cookie helper for SPAs (proxy Sanctum's controller)
        // Frontends should call GET /auth/session/csrf-cookie before POST /auth/session/login
        if (class_exists(CsrfCookieController::class)) {
            Route::get('csrf-cookie', CsrfCookieController::class);
        } else {
            // Fallback no-op if Sanctum route/controller isn’t available
            Route::get('csrf-cookie', fn () => response()->noContent());
        }

        // Session-based auth (web guard)
        Route::post('login',  [SessionAuthController::class, 'login']);
        Route::get('me',      [SessionAuthController::class, 'me'])->middleware('auth:web');
        Route::post('logout', [SessionAuthController::class, 'logout'])->middleware('auth:web');
    });

// Token (api) stack
Route::prefix(config('authx.route_prefix', 'auth'))
    ->middleware(['api'])
    ->group(function () {
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('2fa/status',        [TwoFactorManageController::class, 'status']);
            Route::post('2fa/email',        [TwoFactorManageController::class, 'emailChallenge']);
            Route::post('2fa/sms',          [TwoFactorManageController::class, 'smsChallenge']);
            Route::post('2fa/verify-otp',   [TwoFactorManageController::class, 'verifyOtp']);
            Route::post('2fa/totp/enable',  [TwoFactorManageController::class, 'enableTotp']);
            Route::post('2fa/totp/verify',  [TwoFactorManageController::class, 'verifyTotp']);
            Route::post('2fa/disable',      [TwoFactorManageController::class, 'disable']);
        });
    });

// Session (web) stack
Route::prefix(config('authx.route_prefix', 'auth').'/session')
    ->middleware(['web'])
    ->group(function () {
        Route::middleware('auth:web')->group(function () {
            Route::get('2fa/status',        [TwoFactorManageController::class, 'status']);
            Route::post('2fa/email',        [TwoFactorManageController::class, 'emailChallenge']);
            Route::post('2fa/sms',          [TwoFactorManageController::class, 'smsChallenge']);
            Route::post('2fa/verify-otp',   [TwoFactorManageController::class, 'verifyOtp']);
            Route::post('2fa/totp/enable',  [TwoFactorManageController::class, 'enableTotp']);
            Route::post('2fa/totp/verify',  [TwoFactorManageController::class, 'verifyTotp']);
            Route::post('2fa/disable',      [TwoFactorManageController::class, 'disable']);
        });
    });