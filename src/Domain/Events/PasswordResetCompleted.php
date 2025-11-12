<?php

namespace Rainwaves\LaraAuthSuite\Domain\Events;

use Illuminate\Contracts\Auth\Authenticatable;

class PasswordResetCompleted
{
    public function __construct(public Authenticatable $user) {}
}
