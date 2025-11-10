<?php

namespace Rainwaves\LaraAuthSuite\Services\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Rainwaves\LaraAuthSuite\Contracts\AuthService;

class AuthServiceImpl implements AuthService
{
    /** @var class-string<Model&\Illuminate\Contracts\Auth\Authenticatable> */
    protected string $userModel;

    public function __construct(?string $userModel = null)
    {
        // Allow override via config later (e.g., custom User class)
        $this->userModel = $userModel ?? \App\Models\User::class;
    }

    public function attemptLogin(string $email, string $password): Authenticatable
    {
        /** @var Model&Authenticatable|null $user */
        $user = ($this->userModel)::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }

    public function logout(Authenticatable $user): void
    {
        // Token revocation handled at controller/action via TokenManager
        // Keeping this method for parity with session mode later.
    }
}
