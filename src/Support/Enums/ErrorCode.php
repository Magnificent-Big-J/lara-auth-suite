<?php
namespace Rainwaves\LaraAuthSuite\Support\Enums;

enum ErrorCode: string
{
    case TwoFaRequired = '2fa_required';
    case OtpInvalid    = 'otp_invalid';
    case OtpExpired    = 'otp_expired';
    case RateLimited   = 'rate_limited';
}

