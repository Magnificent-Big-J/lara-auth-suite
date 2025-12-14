<?php

namespace Rainwaves\LaraAuthSuite\Services\TwoFactor;

use Illuminate\Contracts\Auth\Authenticatable;
use Rainwaves\LaraAuthSuite\Contracts\ITwoFactorRequirement;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\ITwoFactorAuth;

readonly class TwoFactorRequirementImpl implements ITwoFactorRequirement
{
    public function __construct(private ITwoFactorAuth $twoFactor) {}

    public function shouldRequire(Authenticatable $user): bool
    {
        $features = (array) config('authx.features', []);
        if (! in_array('two_factor', $features, true)) {
            return false;
        }

        $enforcement = (string) config('authx.2fa.enforcement', 'optional');
        if ($enforcement === 'off') {
            return false;
        }

        if ($enforcement === 'required') {
            return true;
        }

        return $this->twoFactor->isEnabled($user);
    }
}
