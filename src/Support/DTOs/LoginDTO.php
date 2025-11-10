<?php

namespace Rainwaves\LaraAuthSuite\Support\DTOs;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
