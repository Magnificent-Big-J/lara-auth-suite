<?php

namespace Rainwaves\LaraAuthSuite\TwoFactor\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Interface for pluggable two-factor providers.
 *
 * Each provider handles its own channel (email, SMS, TOTP, WebAuthn, etc.)
 * and is used internally by the TwoFactorService to start or verify challenges.
 */
interface TwoFactorProvider
{
    /**
     * Return a unique key identifying this provider.
     * e.g. 'email', 'sms', 'totp', 'webauthn'
     */
    public function key(): string;

    /**
     * Generate and send (or otherwise issue) a verification challenge
     * for the given user.  Implementations should persist a
     * TwoFactorChallenge record and deliver the raw code if applicable.
     */
    public function challenge(Authenticatable $user): void;

    /**
     * Verify a previously issued challenge for the given user.
     * Should increment attempts, respect expiry, and mark consumed
     * on success.  Returns true if the code is valid.
     */
    public function verify(Authenticatable $user, string $code): bool;
}
