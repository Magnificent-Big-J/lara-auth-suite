<?php

namespace Rainwaves\LaraAuthSuite\Support\Helpers;

final class Otpauth
{
    public static function uri(string $issuer, string $account, string $secret, int $period = 30, int $digits = 6): string
    {
        $issuerEnc = rawurlencode($issuer);
        $accountEnc = rawurlencode($account);

        return "otpauth://totp/{$issuerEnc}:{$accountEnc}?secret={$secret}&issuer={$issuerEnc}&period={$period}&digits={$digits}&algorithm=SHA1";
    }
}
