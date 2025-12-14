<?php

namespace Rainwaves\LaraAuthSuite\Contracts;


use Illuminate\Auth\Authenticatable;
use Rainwaves\LaraAuthSuite\DTO\TokenLoginResult;

interface ITokenAuthService
{
    public function login(string $email, string $password): TokenLoginResult;
    public function logout(Authenticatable $user): void;

}
