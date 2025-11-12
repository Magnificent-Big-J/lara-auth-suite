<?php

namespace Rainwaves\LaraAuthSuite\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;

interface AuthService
{
    /** @throws ValidationException on bad credentials */
    public function attemptLogin(string $email, string $password): Authenticatable;

    public function logout(Authenticatable $user): void;
}
