<?php

namespace Rainwaves\LaraAuthSuite\Support\Contracts;

interface ProvidesValidationRules
{
    /** Return an array of validation rules for a named operation, e.g. 'register'. */
    public function for(string $operation): array;
}
