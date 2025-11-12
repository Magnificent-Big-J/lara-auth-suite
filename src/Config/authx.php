<?php

return [
    'route_prefix' => 'auth',
    'mode' => 'both',
    'user_model' => null,

    'features' => ['password_reset', 'two_factor', 'tokens', 'devices'],
    '2fa' => [
        'channels' => ['email'],
        'enforcement' => 'optional',
        'remember_device_days' => 30,
        'otp' => ['expiry_seconds' => 180, 'throttle_per_minute' => 5],
    ],
    'tokens' => ['default_abilities' => ['*'], 'expiry_minutes' => null],
    'throttle' => ['login' => 5, 'two_factor' => 5, 'reset' => 5],
    'registration' => [
        'enabled' => true,
        'issue_token_on_register' => true,     // return PAT on success (token mode)
        'default_roles' => [],                 // e.g. ['client']
        'default_permissions' => [],           // e.g. ['users.view']
        'rules' => [                           // host can override entirely in app/config/authx.php
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['string'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string'],
        ],
        // Optional hook to supply rules dynamically (class must implement ProvidesValidationRules)
        'register_rules_provider' => null, // e.g. \App\Auth\RegisterRules::class
    ],

    'permissions' => [
        'enabled' => true, // if true, try assign roles/permissions via spatie/laravel-permission (if present)
    ],
];
