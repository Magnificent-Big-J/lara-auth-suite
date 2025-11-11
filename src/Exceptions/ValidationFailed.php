<?php

namespace Rainwaves\LaraAuthSuite\Exceptions;

use Illuminate\Validation\ValidationException;

class ValidationFailed extends AuthSuiteException
{
    public static function from(ValidationException $e): self
    {
        return new self($e->getMessage(), previous: $e);
    }
}
