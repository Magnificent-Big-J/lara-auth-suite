<?php

namespace Rainwaves\LaraAuthSuite\Services\Auth;

use Illuminate\Auth\Authenticatable;
use Rainwaves\LaraAuthSuite\Contracts\AuthService;
use Rainwaves\LaraAuthSuite\Contracts\ITwoFactorRequirement;
use Rainwaves\LaraAuthSuite\Contracts\ITokenAuthService;
use Rainwaves\LaraAuthSuite\DTO\TokenLoginResult;
use Rainwaves\LaraAuthSuite\Token\Contracts\TokenManager;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\ITwoFactorAuth;
readonly class TokenAuthServiceImpl implements ITokenAuthService
{
    public function __construct(
        private AuthService $auth,
        private TokenManager $tokens,
        private ITwoFactorRequirement $twoFactorRequirement,
        private ITwoFactorAuth $twoFactor,
    ) {}

    public function login(string $email, string $password): TokenLoginResult
    {
        $user = $this->auth->attemptLogin($email, $password);

        $token = $this->tokens->issue(
            $user,
            config('authx.tokens.default_abilities', ['*']),
            config('authx.tokens.expiry_minutes')
        );

        if ($this->twoFactorRequirement->shouldRequire($user)) {
            $status  = $this->twoFactor->getStatus($user);
            $channel = $status['channel']
                ?: ((array) config('authx.2fa.channels', ['email']))[0]
                    ?: 'email';

            if ($channel === 'email') {
                $this->twoFactor->sendEmailOtp($user);
            }

            return new TokenLoginResult(
                user: $user,
                token: $token,
                requiresTwoFactor: true,
                channel: $channel,
            );
        }

        return new TokenLoginResult(
            user: $user,
            token: $token,
            requiresTwoFactor: false,
            channel: null,
        );
    }

    public function logout(Authenticatable $user): void
    {
        // Revoke only the current access token
        $this->tokens->revokeCurrent($user);

        $this->twoFactor->clearVerified($user);
    }
}
