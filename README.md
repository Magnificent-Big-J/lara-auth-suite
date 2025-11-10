# 🌊 Rainwaves Lara Auth Suite

> **Full-featured Laravel API authentication suite** supporting **Sanctum tokens**, **session mode**, **password resets**, and **2FA (email, SMS, or app authenticator)** — built for modern API and SPA architectures.

---

## 🚀 Overview

**Rainwaves/Lara Auth Suite** gives you plug-and-play authentication for Laravel APIs.  
It unifies Sanctum’s token authentication and Laravel’s session guard, allowing developers to choose their preferred mode — or run both at once.

It’s ideal for:
- SPAs and mobile apps using token-based auth
- Inertia or Blade apps using session-based auth
- Hybrid systems that want both

---

## ✨ Features (Planned)

| Feature | Status | Description |
|----------|---------|-------------|
| **Token-based auth (Sanctum)** | ✅ Planned | Secure API tokens with abilities and expiry. |
| **Session-based auth** | ✅ Planned | Classic Laravel session guard for same-domain SPAs. |
| **Password reset flow** | 🟡 In progress | Secure email-based reset, throttled and auditable. |
| **2FA (Email, SMS, TOTP)** | 🟡 Planned | Add a second factor via email, SMS, or authenticator app. |
| **Recovery codes & trusted devices** | 🟡 Planned | Allow fallback recovery and device remembering. |
| **Token & device management** | 🟡 Planned | List, revoke, and audit active tokens/sessions. |
| **Magic links & passwordless login** | 🔜 Future | Optional passwordless login flow. |

---

## 🧱 Folder Structure

src/
Config/ authx.php # Configuration
Routes/ api.php # Routes (token/session/features)
Http/
Controllers/ # Thin controllers
Middleware/ # Mode/ability enforcement
Requests/ # Validation only
Resources/ # API resource transformers
Actions/ # Core use-cases (LoginUser, IssueToken, etc.)
Domain/
Models/ Policies/ Events/ Listeners/ Notifications/
TwoFactor/
Contracts/ Drivers/ # 2FA providers (EmailOtp, SmsOtp, Totp)
Token/
Contracts/ Sanctum/ # Token logic & integration
Session/
Csrf/ # SPA session helpers
Support/
DTOs/ Enums/ Helpers/
Exceptions/ Console/ Database/ OpenApi/
tests/

---

## ⚙️ Installation

```bash
composer require rainwaves/lara-auth-suite
php artisan vendor:publish --provider="Rainwaves\\LaraAuthSuite\\LaraAuthSuiteServiceProvider" --tag=authx-config

This will create config/authx.php.
🧪 Local Development Setup
To develop or test locally:


Clone the repo
git clone https://github.com/rainwaves/lara-auth-suite.git
cd lara-auth-suite



Install dependencies
composer install



Run tests
php vendor/bin/pest



(Optional) Create a playground Laravel app to test:
composer create-project laravel/laravel authsuite-playground

Then in the app’s composer.json:
{
  "repositories": [
    {
      "type": "path",
      "url": "../lara-auth-suite",
      "options": { "symlink": true }
    }
  ],
  "require": {
    "rainwaves/lara-auth-suite": "*"
  }
}

Finally:
composer update rainwaves/lara-auth-suite
php artisan vendor:publish --provider="Rainwaves\\LaraAuthSuite\\LaraAuthSuiteServiceProvider" --tag=authx-config
php artisan serve

Test via http://localhost:8000/auth/ping.



🧩 Requirements


PHP 8.2 or higher


Laravel 10.x or 11.x


MySQL / PostgreSQL / SQLite


Laravel Sanctum


Optional:


Twilio / Vonage SDKs (for SMS 2FA)


spomky-labs/otphp (for TOTP)


bacon/bacon-qr-code (for QR provisioning)



🗺️ Roadmap
PhaseMilestoneDescription0BootstrapService provider, config, routes, tests (✅ Done)1Token Auth (Sanctum)Login, logout, me2Session ModeCSRF helper for SPAs3Password ResetRequest + reset flows42FA Email OTPBasic 2FA verification52FA TOTPAuthenticator app support62FA SMSTwilio / Vonage driver7Token & Device MgmtRevoke/list/remember devices8Docs & SwaggerOpenAPI annotations9Harden & ReleaseCI, coverage, v1.0.0

🧰 Development Stack


Framework: Laravel


Testing: Pest + Orchestra Testbench


Auth: Sanctum


Docs: Swagger (zircote/swagger-php)


CI: GitHub Actions



🤝 Contributing


Fork the repo


Create your feature branch (git checkout -b feat/awesome)


Commit your changes (git commit -m 'feat: add something awesome')


Push (git push origin feat/awesome)


Create a pull request



🛡️ Security
If you discover a security vulnerability, please email security@rainwaves.dev.
Do not open a public GitHub issue.

📄 License
This package is open-source software licensed under the MIT license.

❤️ Credits
Created with pride by Rainwaves

Building secure, scalable Laravel foundations for modern SaaS platforms.


---

Would you like me to also include **badges** (Packagist version, build status, coverage, etc.) so it’s ready for GitHub?


