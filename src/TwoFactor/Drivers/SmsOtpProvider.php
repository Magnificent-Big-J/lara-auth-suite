<?php
namespace Rainwaves\LaraAuthSuite\TwoFactor\Drivers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Rainwaves\LaraAuthSuite\Domain\Models\TwoFactorChallenge;
use Rainwaves\LaraAuthSuite\Support\Helpers\AgentInfo;
use Rainwaves\LaraAuthSuite\Support\Helpers\Otp;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\TwoFactorProvider;

class SmsOtpProvider implements TwoFactorProvider
{
    public function key(): string { return 'sms'; }

    public function challenge(Authenticatable $user): void
    {
        $code   = Otp::numeric((int) config('authx.2fa.otp.length', 6));
        $ttl    = (int) config('authx.2fa.otp.expiry_seconds', 180);
        $max    = (int) config('authx.2fa.otp.throttle_per_minute', 5);

        TwoFactorChallenge::where('user_id', $user->getAuthIdentifier())->where('channel','sms')->whereNull('consumed_at')->delete();

        TwoFactorChallenge::create([
            'user_id'      => $user->getAuthIdentifier(),
            'channel'      => 'sms',
            'code_hash'    => Hash::make($code),
            'attempts'     => 0,
            'max_attempts' => $max,
            'last_sent_at' => now(),
            'expires_at'   => now()->addSeconds($ttl),
            'meta'         => AgentInfo::snapshot(),
        ]);

        // TODO: integrate Twilio/Vonage/etc. to actually send $code
    }

    public function verify(Authenticatable $user, string $code): bool
    {
        $c = TwoFactorChallenge::where('user_id', $user->getAuthIdentifier())
            ->where('channel','sms')->whereNull('consumed_at')
            ->where('expires_at','>',now())->latest('id')->first();

        if (! $c || ! $c->canAttempt()) return false;
        $c->incrementAttempts();

        if (! $c->verifyCode($code)) return false;
        $c->markConsumed();
        return true;
    }
}
