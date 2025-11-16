<?php

namespace Rainwaves\LaraAuthSuite\TwoFactor\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface ITwoFactorAuth
{
    public function isEnabled(Authenticatable $user): bool;

    public function sendEmailOtp(Authenticatable $user): string;

    public function sendSmsOtp(Authenticatable $user, string $phoneNumber): string;

    public function enableAuthenticatorApp(Authenticatable $user): string;

    public function verifyAuthenticatorApp(Authenticatable $user, string $code): bool;

    public function disableTwoFactor(Authenticatable $user): void;

    public function enableEmailOtp(Authenticatable $user): void;

    public function markVerified(Authenticatable $user): void;

    public function isVerified(Authenticatable $user): bool;

    public function verifyOtp(Authenticatable $user, string $code): bool;

    /** 'totp', 'email', or null */
    public function currentChannel(Authenticatable $user): ?string;

    /** Convenience payload: ['enabled' => bool, 'verified' => bool, 'channel' => ?string] */
    public function getStatus(Authenticatable $user): array;
}
