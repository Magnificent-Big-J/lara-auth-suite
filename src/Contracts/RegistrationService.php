<?php

namespace Rainwaves\LaraAuthSuite\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface RegistrationService
{
    /**
     * @param  array{name:string,email:string,password:string,roles?:array<string>,permissions?:array<string>}  $data
     */
    public function register(array $data): Authenticatable;
}
