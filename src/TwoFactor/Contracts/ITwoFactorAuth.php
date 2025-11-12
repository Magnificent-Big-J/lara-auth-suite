<?php

namespace Rainwaves\LaraAuthSuite\TwoFactor\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface ITwoFactorAuth
{
    public function isVerified(Authenticatable $user): bool;

    public function markVerified(Authenticatable $user): void;

    /** Quick check the project can use to branch login flow. */
    public function isEnabled(Authenticatable $user): bool;

    /** Issue and store a hashed OTP for login (email channel). Returns the RAW code. */
    public function sendEmailOtp(Authenticatable $user): string;

    /** Issue and store a hashed OTP for login (sms channel). Returns the RAW code. */
    public function sendSmsOtp(Authenticatable $user, string $phoneNumber): string;

    /** Verify a previously issued OTP (email or sms). Marks it used on success. */
    public function verifyOtp(Authenticatable $user, string $code): bool;

    /** Begin authenticator-app setup: create & persist secret (BASE32), mark disabled until confirmed. */
    public function enableAuthenticatorApp(Authenticatable $user): string;

    /** Confirm authenticator-app by verifying a TOTP code, enabling 2FA if valid. */
    public function verifyAuthenticatorApp(Authenticatable $user, string $code): bool;

    /** Disable any 2FA and clear secrets/OTPs. */
    public function disableTwoFactor(Authenticatable $user): void;

    /** Explicit opt-in to Email OTP (e.g., in profile settings). */
    public function enableEmailOtp(Authenticatable $user): void;
}
