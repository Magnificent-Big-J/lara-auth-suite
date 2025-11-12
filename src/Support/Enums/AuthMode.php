<?php

namespace Rainwaves\LaraAuthSuite\Support\Enums;

enum AuthMode: string
{
    case Token = 'token';
    case Session = 'session';
    case Both = 'both';
}
