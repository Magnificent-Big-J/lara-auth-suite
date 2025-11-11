<?php
namespace Rainwaves\LaraAuthSuite\Support\Helpers;

final class Otp
{
    public static function numeric(int $length = 6): string
    {
        $max = 10 ** $length - 1;
        return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
    }
}
