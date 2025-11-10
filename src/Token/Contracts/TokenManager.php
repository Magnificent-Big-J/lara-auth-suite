<?php

namespace Rainwaves\LaraAuthSuite\Token\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface TokenManager
{
    /** Issue a new token for user with abilities; return plain text token */
    public function issue(Authenticatable $user, array $abilities = [], ?int $expiresMinutes = null): string;

    /** Revoke current access token for the request’s user (if any) */
    public function revokeCurrent(?Authenticatable $user = null): void;

    /** Revoke all tokens for a user (e.g., on password reset) */
    public function revokeAll(Authenticatable $user): void;
}
