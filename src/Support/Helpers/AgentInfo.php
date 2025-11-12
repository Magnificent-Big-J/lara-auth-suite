<?php

namespace Rainwaves\LaraAuthSuite\Support\Helpers;

final class AgentInfo
{
    public static function snapshot(): array
    {
        $req = request();

        return ['ip' => $req?->ip(), 'ua' => $req?->userAgent()];
    }
}
