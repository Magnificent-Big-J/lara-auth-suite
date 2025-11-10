<?php

use Orchestra\Testbench\TestCase as BaseTestCase;
use Rainwaves\LaraAuthSuite\LaraAuthSuiteServiceProvider;

uses(BaseTestCase::class)->in('Feature', 'Unit');

function packageProviders($app)
{
    return [LaraAuthSuiteServiceProvider::class];
}
