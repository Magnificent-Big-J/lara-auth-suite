<?php

return [
    'route_prefix' => 'auth',            // /auth/...
    'mode' => 'both',                    // token|session|both
    'features' => [                      // toggle later
        'password_reset',
        'two_factor',
        'tokens',
        'devices',
    ],
    '2fa' => [
        'channels' => ['email'],         // email|sms|totp
        'enforcement' => 'optional',     // optional|recommended|required
        'remember_device_days' => 30,
        'otp' => [
            'expiry_seconds' => 180,
            'throttle_per_minute' => 5,
        ],
    ],
    'tokens' => [
        'default_abilities' => ['*'],
        'expiry_minutes' => null,
    ],
    'throttle' => [
        'login' => 5,
        'two_factor' => 5,
        'reset' => 5,
    ],
];
