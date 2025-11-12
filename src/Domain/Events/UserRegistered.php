<?php

namespace Rainwaves\LaraAuthSuite\Domain\Events;

use Illuminate\Contracts\Auth\Authenticatable;

class UserRegistered
{
    public function __construct(public Authenticatable $user) {}
}
