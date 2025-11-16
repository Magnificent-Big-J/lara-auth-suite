<?php

namespace Rainwaves\LaraAuthSuite\Services\TwoFactor;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache; // 👈 ADD THIS
use Rainwaves\LaraAuthSuite\Domain\Models\TwoFactorChallenge;
use Rainwaves\LaraAuthSuite\Domain\Models\TwoFactorSecret;
use Rainwaves\LaraAuthSuite\Domain\Notifications\TwoFactorEmailCode;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\ITwoFactorAuth;

/**
 * Facade service orchestrating Email/SMS OTP and TOTP secrets.
 * Note: SMS + TOTP providers can be swapped in later without changing this API.
 */
class TwoFactorAuthService implements ITwoFactorAuth
{
    public function isEnabled(Authenticatable $user): bool
    {
        return TwoFactorSecret::where('user_id', $user->getAuthIdentifier())
            ->whereNull('revoked_at')
            ->where(function ($q) {
                $q->whereNotNull('enabled_at')   // e.g., totp
                ->orWhere('type', 'email');    // email OTP considered enabled once opted-in
            })
            ->exists();
    }

    public function sendEmailOtp(Authenticatable $user): string
    {
        $len  = (int) config('authx.2fa.otp.length', 6);
        $code = str_pad((string) random_int(0, (10 ** $len) - 1), $len, '0', STR_PAD_LEFT);
        $ttl  = (int) config('authx.2fa.otp.expiry_seconds', 180);
        $max  = (int) config('authx.2fa.otp.throttle_per_minute', 5);

        // clear any outstanding unconsumed email challenges
        TwoFactorChallenge::where('user_id', $user->getAuthIdentifier())
            ->where('channel', 'email')
            ->whereNull('consumed_at')
            ->delete();

        TwoFactorChallenge::create([
            'user_id'      => $user->getAuthIdentifier(),
            'channel'      => 'email',
            'code_hash'    => Hash::make($code),
            'attempts'     => 0,
            'max_attempts' => $max,
            'last_sent_at' => now(),
            'expires_at'   => now()->addSeconds($ttl),
            'meta'         => ['ip' => request()->ip(), 'ua' => request()->userAgent()],
        ]);

        $user->notify(new TwoFactorEmailCode($code, $ttl));

        return $code;
    }

    public function sendSmsOtp(Authenticatable $user, string $phoneNumber): string
    {
        $len  = (int) config('authx.2fa.otp.length', 6);
        $code = str_pad((string) random_int(0, (10 ** $len) - 1), $len, '0', STR_PAD_LEFT);
        $ttl  = (int) config('authx.2fa.otp.expiry_seconds', 180);
        $max  = (int) config('authx.2fa.otp.throttle_per_minute', 5);

        TwoFactorChallenge::where('user_id', $user->getAuthIdentifier())
            ->where('channel', 'sms')
            ->whereNull('consumed_at')
            ->delete();

        TwoFactorChallenge::create([
            'user_id'      => $user->getAuthIdentifier(),
            'channel'      => 'sms',
            'code_hash'    => Hash::make($code),
            'attempts'     => 0,
            'max_attempts' => $max,
            'last_sent_at' => now(),
            'expires_at'   => now()->addSeconds($ttl),
            'meta'         => [
                'ip'    => request()->ip(),
                'ua'    => request()->userAgent(),
                'phone' => $phoneNumber,
            ],
        ]);

        // plug into your SMS provider here

        return $code;
    }

    public function enableAuthenticatorApp(Authenticatable $user): string
    {
        $raw    = random_bytes(20);
        $base32 = rtrim($this->base32Encode($raw), '=');

        TwoFactorSecret::updateOrCreate(
            ['user_id' => $user->getAuthIdentifier(), 'type' => 'totp'],
            ['secret' => $base32, 'enabled_at' => null, 'revoked_at' => null]
        );

        return $base32;
    }

    public function verifyAuthenticatorApp(Authenticatable $user, string $code): bool
    {
        $secret = TwoFactorSecret::where('user_id', $user->getAuthIdentifier())
            ->where('type', 'totp')
            ->whereNull('revoked_at')
            ->first();

        if (! $secret || empty($secret->secret)) {
            return false;
        }

        // Normalise user input (remove spaces etc.)
        $code   = preg_replace('/\s+/', '', trim($code));
        $digits = (int) config('authx.2fa.totp_digits', 6);
        $period = (int) config('authx.2fa.totp_period', 30); // 30s steps
        $window = (int) config('authx.2fa.totp_window', 1); // +/- 1 step

        $now   = time();
        $valid = false;

        // Check current time-step and a small window around it
        for ($i = -$window; $i <= $window; $i++) {
            $counter = (int) floor(($now / $period)) + $i;

            if ($this->verifyTotpForCounter($secret->secret, $code, $counter, $digits)) {
                $valid = true;
                break;
            }
        }

        if (! $valid) {
            return false;
        }

        // Mark TOTP as enabled
        $secret->enabled_at = now();
        $secret->save();

        // Mark this session / token as 2FA-verified
        $this->markVerified($user);

        return true;
    }

    public function disableTwoFactor(Authenticatable $user): void
    {
        TwoFactorSecret::where('user_id', $user->getAuthIdentifier())
            ->update(['revoked_at' => now(), 'enabled_at' => null]);

        TwoFactorChallenge::where('user_id', $user->getAuthIdentifier())
            ->whereNull('consumed_at')
            ->delete();
    }

    public function enableEmailOtp(Authenticatable $user): void
    {
        TwoFactorSecret::updateOrCreate(
            ['user_id' => $user->getAuthIdentifier(), 'type' => 'email'],
            ['enabled_at' => now(), 'revoked_at' => null]
        );
    }

    public function markVerified(Authenticatable $user): void
    {
        $ttl = (int) config('authx.2fa.verification_ttl_seconds', 600);
        Cache::put($this->verificationCacheKey($user), true, $ttl);
    }

    public function isVerified(Authenticatable $user): bool
    {
        return (bool) Cache::get($this->verificationCacheKey($user), false);
    }

    protected function verificationCacheKey(Authenticatable $user): string
    {
        $tokenId = method_exists($user, 'currentAccessToken') && $user->currentAccessToken()
            ? $user->currentAccessToken()->id
            : null;

        return $tokenId
            ? "authx:2fa:token:{$tokenId}"
            : 'authx:2fa:session:' . (session()->getId() ?? 'anon');
    }

    public function verifyOtp(Authenticatable $user, string $code): bool
    {
        $challenge = TwoFactorChallenge::where('user_id', $user->getAuthIdentifier())
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (! $challenge || ! $challenge->canAttempt()) {
            return false;
        }

        $challenge->incrementAttempts();

        if (! $challenge->verifyCode($code)) {
            return false;
        }

        $challenge->markConsumed();

        if ($challenge->channel === 'email') {
            TwoFactorSecret::updateOrCreate(
                ['user_id' => $user->getAuthIdentifier(), 'type' => 'email'],
                ['enabled_at' => now(), 'secret' => null]
            );
        }

        $this->markVerified($user);

        return true;
    }

    // ── Base32 helpers ──────────────────────────────────

    protected function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits     = '';

        foreach (str_split($data) as $c) {
            $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
        }

        $output = '';

        foreach (str_split($bits, 5) as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0');
            }
            $output .= $alphabet[bindec($chunk)];
        }

        while (strlen($output) % 8 !== 0) {
            $output .= '=';
        }

        return $output;
    }

    protected function base32Decode(string $b32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

        $b32 = strtoupper($b32);
        $b32 = preg_replace('/[^A-Z2-7]/', '', $b32);

        $bits = '';
        foreach (str_split($b32) as $char) {
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                continue;
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $output = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) < 8) {
                continue;
            }
            $output .= chr(bindec($byte));
        }

        return $output;
    }

    protected function verifyTotpForCounter(string $base32Secret, string $userCode, int $counter, int $digits = 6): bool
    {
        $secret = $this->base32Decode($base32Secret);

        if ($secret === '') {
            return false;
        }

        // 8-byte counter (RFC 4226)
        $binaryCounter = pack('N*', 0) . pack('N*', $counter);

        $hash   = hash_hmac('sha1', $binaryCounter, $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;

        $segment  = substr($hash, $offset, 4);
        $value    = unpack('N', $segment)[1] & 0x7fffffff;
        $modulo   = 10 ** $digits;
        $expected = str_pad((string) ($value % $modulo), $digits, '0', STR_PAD_LEFT);

        return hash_equals($expected, $userCode);
    }

    // ── Channel helpers ─────────────────────────────────

    public function currentChannel(Authenticatable $user): ?string
    {
        $userId = $user->getAuthIdentifier();

        // Prefer TOTP if it is enabled
        $hasTotp = TwoFactorSecret::where('user_id', $userId)
            ->where('type', 'totp')
            ->whereNull('revoked_at')
            ->whereNotNull('enabled_at')
            ->exists();

        if ($hasTotp) {
            return 'totp';
        }

        // Fallback to Email if it is enabled
        $hasEmail = TwoFactorSecret::where('user_id', $userId)
            ->where('type', 'email')
            ->whereNull('revoked_at')
            ->whereNotNull('enabled_at')
            ->exists();

        if ($hasEmail) {
            return 'email';
        }

        return null;
    }

    public function getStatus(Authenticatable $user): array
    {
        return [
            'enabled'  => $this->isEnabled($user),
            'verified' => $this->isVerified($user),
            'channel'  => $this->currentChannel($user),
        ];
    }
}
