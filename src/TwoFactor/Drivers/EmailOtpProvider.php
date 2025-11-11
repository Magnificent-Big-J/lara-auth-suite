<?php

namespace Rainwaves\LaraAuthSuite\TwoFactor\Drivers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\TwoFactorProvider;
use Rainwaves\LaraAuthSuite\Domain\Models\TwoFactorChallenge;
use Rainwaves\LaraAuthSuite\Domain\Notifications\TwoFactorEmailCode;

class EmailOtpProvider implements TwoFactorProvider
{
    public function key(): string { return 'email'; }

    public function challenge(Authenticatable $user): void
    {
        $len = (int) config('authx.2fa.otp.length', 6);
        $code = str_pad((string) random_int(0, (10 ** $len) - 1), $len, '0', STR_PAD_LEFT);

        $expires = now()->addSeconds((int) config('authx.2fa.otp.expiry_seconds', 180));

        // Either create a new record or refresh latest unconsumed
        TwoFactorChallenge::where('user_id', $user->getAuthIdentifier())
            ->where('channel', 'email')
            ->whereNull('consumed_at')
            ->delete();

        TwoFactorChallenge::create([
            'user_id'     => $user->getAuthIdentifier(),
            'channel'     => 'email',
            'code_hash'   => Hash::make($code),
            'attempts'    => 0,
            'max_attempts'=> (int) config('authx.2fa.otp.throttle_per_minute', 5),
            'last_sent_at'=> now(),
            'expires_at'  => $expires,
            'meta'        => ['ip' => request()->ip(), 'ua' => request()->userAgent()],
        ]);

        // Send code via email
        $user->notify(new TwoFactorEmailCode($code, $expires));
    }

    public function verify(Authenticatable $user, string $code): bool
    {
        /** @var TwoFactorChallenge|null $challenge */
        $challenge = TwoFactorChallenge::where('user_id', $user->getAuthIdentifier())
            ->where('channel', 'email')
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (! $challenge) {
            return false;
        }

        if (! $challenge->canAttempt()) {
            return false;
        }

        $challenge->incrementAttempts();

        if (! $challenge->verifyCode($code)) {
            return false;
        }

        $challenge->markConsumed();
        return true;
    }
}
