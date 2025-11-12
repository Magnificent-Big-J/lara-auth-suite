<?php

namespace Rainwaves\LaraAuthSuite\Services\TwoFactor;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
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
        $len = (int) config('authx.2fa.otp.length', 6);
        $code = str_pad((string) random_int(0, (10 ** $len) - 1), $len, '0', STR_PAD_LEFT);
        $ttl = (int) config('authx.2fa.otp.expiry_seconds', 180);
        $max = (int) config('authx.2fa.otp.throttle_per_minute', 5);

        // clear any outstanding unconsumed email challenges
        TwoFactorChallenge::where('user_id', $user->getAuthIdentifier())
            ->where('channel', 'email')
            ->whereNull('consumed_at')
            ->delete();

        TwoFactorChallenge::create([
            'user_id' => $user->getAuthIdentifier(),
            'channel' => 'email',
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'max_attempts' => $max,
            'last_sent_at' => now(),
            'expires_at' => now()->addSeconds($ttl),
            'meta' => ['ip' => request()->ip(), 'ua' => request()->userAgent()],
        ]);

        // notify via email
        $user->notify(new TwoFactorEmailCode($code, now()->addSeconds($ttl)));

        return $code;
    }

    public function sendSmsOtp(Authenticatable $user, string $phoneNumber): string
    {
        // Placeholder: wire your Sms provider/driver here and send the raw $code
        $len = (int) config('authx.2fa.otp.length', 6);
        $code = str_pad((string) random_int(0, (10 ** $len) - 1), $len, '0', STR_PAD_LEFT);
        $ttl = (int) config('authx.2fa.otp.expiry_seconds', 180);
        $max = (int) config('authx.2fa.otp.throttle_per_minute', 5);

        TwoFactorChallenge::where('user_id', $user->getAuthIdentifier())
            ->where('channel', 'sms')
            ->whereNull('consumed_at')
            ->delete();

        TwoFactorChallenge::create([
            'user_id' => $user->getAuthIdentifier(),
            'channel' => 'sms',
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'max_attempts' => $max,
            'last_sent_at' => now(),
            'expires_at' => now()->addSeconds($ttl),
            'meta' => ['ip' => request()->ip(), 'ua' => request()->userAgent(), 'phone' => $phoneNumber],
        ]);

        // TODO: dispatch SMS via your SmsSender here.

        return $code;
    }

    public function enableAuthenticatorApp(Authenticatable $user): string
    {

        $raw = random_bytes(20);
        $base32 = rtrim($this->base32Encode($raw), '=');

        TwoFactorSecret::updateOrCreate(
            ['user_id' => $user->getAuthIdentifier(), 'type' => 'totp'],
            ['secret' => $base32, 'enabled_at' => null, 'revoked_at' => null]
        );

        return $base32;
    }

    public function verifyAuthenticatorApp(Authenticatable $user, string $code): bool
    {
        // TODO: Replace with real TOTP verify using otphp:
        // $secret = TwoFactorSecret::for user,type=totp; verify $code; if ok, set enabled_at=now
        $secret = TwoFactorSecret::where('user_id', $user->getAuthIdentifier())
            ->where('type', 'totp')
            ->whereNull('revoked_at')
            ->first();

        if (! $secret || empty($secret->secret)) {
            return false;
        }

        // Minimal placeholder (ALWAYS replace with proper TOTP library verification)
        // This placeholder intentionally returns false to avoid false sense of security.
        return false;
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

    /** Mark this session/token as 2FA-verified for a short TTL (step-up). */
    public function markVerified(Authenticatable $user): void
    {
        $ttl = (int) config('authx.2fa.verification_ttl_seconds', 600);
        Cache::put($this->verificationCacheKey($user), true, $ttl);
    }

    /** Check if current session/token is within verified window. */
    public function isVerified(Authenticatable $user): bool
    {
        return (bool) Cache::get($this->verificationCacheKey($user), false);
    }

    /** Where to store the “step-up verified” flag (token-aware or session-aware). */
    protected function verificationCacheKey(Authenticatable $user): string
    {
        // If using Sanctum PATs (token mode), bind to token id; otherwise bind to session id (session mode)
        $tokenId = method_exists($user, 'currentAccessToken') && $user->currentAccessToken()
            ? $user->currentAccessToken()->id
            : null;

        return $tokenId
            ? "authx:2fa:token:{$tokenId}"
            : 'authx:2fa:session:'.(session()->getId() ?? 'anon');
    }

    // 🔁 call markVerified() when OTP verification succeeds
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

        // ✅ mark this token/session as step-up verified
        $this->markVerified($user);

        return true;
    }

    /** Lightweight Base32 encoder (RFC 4648). */
    protected function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
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
}
