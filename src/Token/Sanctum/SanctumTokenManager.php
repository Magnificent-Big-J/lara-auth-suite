<?php

namespace Rainwaves\LaraAuthSuite\Token\Sanctum;

use Illuminate\Contracts\Auth\Authenticatable;
use Rainwaves\LaraAuthSuite\Token\Contracts\TokenManager;

class SanctumTokenManager implements TokenManager
{
    public function issue(Authenticatable $user, array $abilities = [], ?int $expiresMinutes = null): string
    {
        $token = $user->createToken('api-token', $abilities);
        // Sanctum expiry is usually handled via token middleware or custom policies;
        // we’ll extend this later if you choose time-bound tokens.
        return $token->plainTextToken;
    }

    public function revokeCurrent(?Authenticatable $user = null): void
    {
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }

    public function revokeAll(Authenticatable $user): void
    {
        $user->tokens()?->delete();
    }
}
