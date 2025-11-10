<?php

use Orchestra\Testbench\TestCase as BaseTestCase;

uses(BaseTestCase::class)->in('Feature', 'Unit');

function packageProviders($app)
{
    return [\Rainwaves\LaraAuthSuite\LaraAuthSuiteServiceProvider::class];
}
