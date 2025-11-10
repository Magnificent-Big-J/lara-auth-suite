<?php

namespace Rainwaves\LaraAuthSuite\Services\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Rainwaves\LaraAuthSuite\Contracts\SessionAuthService;

readonly class SessionAuthServiceImpl implements SessionAuthService
{
    /** @return void */
    public function __construct(private string $userModel) {}

    public function login(string $email, string $password): Authenticatable
    {
        $user = ($this->userModel)::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        Auth::guard('web')->login($user, remember: false);

        return $user;
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
