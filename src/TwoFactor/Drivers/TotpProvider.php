<?php

namespace Rainwaves\LaraAuthSuite\TwoFactor\Drivers;

use Illuminate\Contracts\Auth\Authenticatable;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\TwoFactorProvider;

class TotpProvider implements TwoFactorProvider
{
    public function key(): string
    {
        return 'totp';
    }

    public function challenge(Authenticatable $user): void
    {
        // For TOTP we typically show a QR (otpauth://) rather than send a code.
        // No-op. Provision via TwoFactorManageController@enableTotp.
    }

    public function verify(Authenticatable $user, string $code): bool
    {
        // TODO: Implement real TOTP verification (spomky-labs/otphp).
        return false;
    }
}
