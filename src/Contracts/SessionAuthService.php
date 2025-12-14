<?php

namespace Rainwaves\LaraAuthSuite\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;
use Rainwaves\LaraAuthSuite\DTO\SessionLoginResult;

interface SessionAuthService
{
    /** @throws ValidationException on bad credentials */
    public function login(string $email, string $password): SessionLoginResult;

    public function current(): ?Authenticatable;

    public function logout(): void;
}
