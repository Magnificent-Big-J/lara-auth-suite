<?php

namespace Rainwaves\LaraAuthSuite\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface ITwoFactorRequirement
{
    public function shouldRequire(Authenticatable $user): bool;
}
