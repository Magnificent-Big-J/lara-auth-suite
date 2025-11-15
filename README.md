🌊 Rainwaves Lara Auth Suite

Modern, flexible authentication for Laravel APIs & SPAs.








Plug-and-play authentication for Laravel 10/11, supporting both API token auth (Sanctum) and session-based auth for SPAs — with password resets, optional 2FA, and full role/permission support.

🚀 Overview

Rainwaves/Lara Auth Suite gives you full authentication without writing boilerplate:

Token authentication for mobile apps or external APIs

Session authentication for SPAs (Vue / React / Inertia / Livewire)

Unified password reset flow

Optional Two-Factor Authentication (email/SMS/TOTP)

Automatic role & permission assignment (Spatie Permissions)

This is ideal for:

SaaS platforms

Admin dashboards

Multi-tenant SPAs

Hybrid apps needing both tokens + sessions

✨ Features
Feature	Status	Description
Sanctum PAT login	✅ Done	Token-based API authentication.
Session authentication	✅ Done	Classic Laravel guard for SPA + CSRF.
Password reset (email)	✅ Done	Full reset-link + throttling.
2FA: Email OTP	🔄 Partial	Enabled if configured.
2FA: TOTP (Google Authenticator)	🔜 Planned	QR provisioning, verification.
2FA: SMS (Twilio/Vonage)	🔜 Planned	Configurable SMS provider.
Trusted devices	🔜 Planned	Device remembering.
Token/session/device management	🔜 Planned	Revoke + audit.
⚙️ Installation
composer require rainwaves/lara-auth-suite


Then publish the config:

php artisan vendor:publish --provider="Rainwaves\\LaraAuthSuite\\LaraAuthSuiteServiceProvider" --tag=authx-config


This publishes:

config/authx.php

📦 Usage

Below are the built-in endpoints provided by the package.

🔐 1. Login (Session Mode)

POST /auth/login

{
"email": "admin@example.com",
"password": "secret",
"remember": true
}


Response:

{
"status": "ok",
"user": { ... }
}

🔑 2. Login (Token Mode / API Clients)

POST /auth/token/login

Response:

{
"token": "plain-text-token",
"abilities": ["*"]
}

🙋‍♂️ 3. Get Current User

Requires either:
✔ Session cookie
or
✔ Bearer token

GET /auth/me

🚪 4. Logout

Session:

POST /auth/logout


Token:

POST /auth/token/logout

🔁 5. Forgot Password
POST /auth/password/forgot


Payload:

{ "email": "admin@example.com" }

🔒 6. Reset Password
POST /auth/password/reset


Payload:

{
"email": "admin@example.com",
"token": "reset-token",
"password": "newpassword",
"password_confirmation": "newpassword"
}

🛡️ 7. Two-Factor Authentication (Optional)
Email OTP
POST /auth/session/2fa/email
POST /auth/session/2fa/verify-otp
POST /auth/session/2fa/disable

TOTP (future)
POST /auth/session/2fa/totp/enable
POST /auth/session/2fa/totp/verify

🔧 Configuration (config/authx.php)

Example:

return [
'route_prefix' => 'auth',
'mode' => 'both', // session, token, both

    'features' => [
        'password_reset',
        'two_factor',
        'tokens',
        'devices',
    ],

    'registration' => [
        'enabled' => true,
        'issue_token_on_register' => true,
        'default_roles' => [],
        'default_permissions' => [],
    ],

    '2fa' => [
        'channels' => ['email'],
        'enforcement' => 'optional',
    ],
];

🧪 Local Playground Development

To develop the package locally, symlink using Composer’s "path" repo:

"repositories": [
{
"type": "path",
"url": "packages/lara-auth-suite",
"options": { "symlink": true }
}
]


Then:

composer update rainwaves/lara-auth-suite

🧩 Frontend Integration (SPA)

Full frontend documentation (Vue 3 / Nuxt / React) will be provided in a separate guide.
This includes:

login flow

session cookies

CSRF

forgot/reset

OTP verification

storing user + abilities in Pinia/Zustand

👉 We’ll create a NEW CHAT only for frontend so you can paste code directly into your SPA.

🛣 Roadmap
Phase	Feature
1	Token auth (done)
2	Session auth (done)
3	Password reset (done)
4	2FA Email
5	2FA TOTP
6	2FA SMS
7	Trusted devices
8	Token/session management
9	Complete frontend documentation
10	v1.0 stable release
🛡 Security

If you discover a security issue, please email:

security@rainwaves.dev

📄 License

MIT © Rainwaves

❤️ Credits

Created with love by Rainwaves
Building secure, modern SaaS authentication for Laravel.

✅ READY TO CONTINUE?

If you're ready:
👉 Say: “Start the frontend-only chat”
and I’ll open a clean conversation dedicated to:

Vue 3 login page

Pinia auth store

Forgot password page

Reset password page

Users list (admin only)

Roles/permissions integration

SPA session flow, CSRF, cookies

2FA UI

Auto-refresh & bootstrap logic

Your entire frontend will be production ready.
