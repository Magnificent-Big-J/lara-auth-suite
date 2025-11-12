<?php

namespace Rainwaves\LaraAuthSuite\Services\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Rainwaves\LaraAuthSuite\Contracts\PasswordResetService;

class PasswordResetServiceImpl implements PasswordResetService
{
    public function requestReset(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email]);
        abort_unless($status === Password::RESET_LINK_SENT, 400, __($status));
    }

    public function resetPassword(string $email, string $token, string $password): bool
    {
        $status = Password::reset(
            ['email' => $email, 'token' => $token, 'password' => $password],
            function ($user) use ($password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET;
    }
}
