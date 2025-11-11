<?php
namespace Rainwaves\LaraAuthSuite\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\ITwoFactorAuth;

readonly class VerifyTwoFactor
{
    public function __construct(private ITwoFactorAuth $twofa) {}
    public function __invoke(Authenticatable $user, string $code): bool { return $this->twofa->verifyOtp($user, $code); }
}
