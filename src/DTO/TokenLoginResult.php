<?php

namespace Rainwaves\LaraAuthSuite\DTO;

use Illuminate\Contracts\Auth\Authenticatable;

readonly class TokenLoginResult
{
    public function __construct(
        public Authenticatable $user,
        public string          $token,
        public bool            $requiresTwoFactor,
        public ?string         $channel = null,
        public string          $tokenType = 'Bearer',
    ) {}
}
