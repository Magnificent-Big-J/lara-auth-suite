<?php

namespace Rainwaves\LaraAuthSuite\DTO;

use Illuminate\Contracts\Auth\Authenticatable;

readonly class SessionLoginResult
{
    public function __construct(
        public Authenticatable $user,
        public bool            $requiresTwoFactor,
        public ?string         $channel = null, // email|totp|null
    ) {}
}
