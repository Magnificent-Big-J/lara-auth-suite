<?php

namespace Rainwaves\LaraAuthSuite\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;

interface SessionAuthService
{
    /** @throws ValidationException on bad credentials */
    public function login(string $email, string $password): Authenticatable;

    public function current(): ?Authenticatable;

    public function logout(): void;
}
