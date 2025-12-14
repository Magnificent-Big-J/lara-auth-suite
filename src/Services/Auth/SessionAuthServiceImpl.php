<?php

namespace Rainwaves\LaraAuthSuite\Services\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Rainwaves\LaraAuthSuite\Contracts\ITwoFactorRequirement;
use Rainwaves\LaraAuthSuite\Contracts\SessionAuthService;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\ITwoFactorAuth;
use Rainwaves\LaraAuthSuite\DTO\SessionLoginResult;
readonly class SessionAuthServiceImpl implements SessionAuthService
{
    /** @param class-string<Model&Authenticatable> $userModel */
    public function __construct(
        private string $userModel,
        private ITwoFactorRequirement $twoFactorRequirement,
        private ITwoFactorAuth $twoFactor,
    ) {}

    public function login(string $email, string $password): SessionLoginResult
    {
        /** @var Model&Authenticatable|null $user */
        $user = ($this->userModel)::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // login session
        Auth::guard('web')->login($user, remember: false);
        request()->session()->regenerate();

        // enforce 2FA policy
        if ($this->twoFactorRequirement->shouldRequire($user)) {
            $this->twoFactor->clearVerified($user);

            $status = $this->twoFactor->getStatus($user);

            // “intuitive” default: auto-send OTP if email channel
            if ($status['channel'] === 'email') {
                $this->twoFactor->sendEmailOtp($user);
            }

            return new SessionLoginResult(
                user: $user,
                requiresTwoFactor: true,
                channel: $status['channel'],
            );
        }

        // no 2FA needed → mark verified for session
        $this->twoFactor->markVerified($user);

        return new SessionLoginResult(
            user: $user,
            requiresTwoFactor: false,
            channel: null
        );
    }

    public function current(): ?Authenticatable
    {
        return Auth::guard('web')->user();
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
