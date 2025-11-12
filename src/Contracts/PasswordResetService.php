<?php

namespace Rainwaves\LaraAuthSuite\Contracts;

interface PasswordResetService
{
    public function requestReset(string $email): void;

    public function resetPassword(string $email, string $token, string $password): bool;
}
