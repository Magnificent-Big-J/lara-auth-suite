<?php

namespace Rainwaves\LaraAuthSuite\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\ITwoFactorAuth;

readonly class StartTwoFactor
{
    public function __construct(private ITwoFactorAuth $twofa) {}

    public function __invoke(Authenticatable $user, string $channel = 'email'): void
    {
        $channel === 'sms' ? $this->twofa->sendSmsOtp($user, (string) ($user->phone ?? '')) : $this->twofa->sendEmailOtp($user);
    }
}
