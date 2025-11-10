# 🌊 Rainwaves Lara Auth Suite

[![Packagist Version](https://img.shields.io/packagist/v/rainwaves/lara-auth-suite.svg?style=flat-square)](https://packagist.org/packages/rainwaves/lara-auth-suite)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.2-blue?style=flat-square&logo=php)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-10%2F11-red?style=flat-square&logo=laravel)](https://laravel.com)
[![Build Status](https://img.shields.io/github/actions/workflow/status/rainwaves/lara-auth-suite/tests.yml?label=tests&style=flat-square)](https://github.com/rainwaves/lara-auth-suite/actions)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)

> **Full-featured Laravel API authentication suite** supporting **Sanctum tokens**, **session mode**, **password resets**, and **2FA (email, SMS, or app authenticator)** — built for modern API and SPA architectures.

---

## 🚀 Overview

**Rainwaves/Lara Auth Suite** gives you plug-and-play authentication for Laravel APIs.  
It unifies Sanctum’s token authentication and Laravel’s session guard, allowing developers to choose their preferred mode — or even run both at once.

**Perfect for:**
- SPAs and mobile apps using token-based auth
- Inertia or Blade apps using session-based auth
- Hybrid systems that need both

---

## ✨ Features (Planned)

| Feature | Status | Description |
|----------|---------|-------------|
| **Token-based auth (Sanctum)** | ✅ Done | Secure API tokens with abilities and expiry. |
| **Session-based auth** | ✅ Done | Classic Laravel session guard for same-domain SPAs. |
| **Password reset flow** | 🟡 In progress | Secure email-based reset, throttled and auditable. |
| **2FA (Email, SMS, TOTP)** | 🟡 Planned | Add a second factor via email, SMS, or authenticator app. |
| **Recovery codes & trusted devices** | 🟡 Planned | Allow fallback recovery and device remembering. |
| **Token & device management** | 🟡 Planned | List, revoke, and audit active tokens/sessions. |
| **Magic links & passwordless login** | 🔜 Future | Optional passwordless login flow. |

---

## 🧱 Folder Structure

src/
├─ Config/ authx.php # Configuration
├─ Routes/ api.php # Routes (token/session/features)
├─ Http/
│ ├─ Controllers/ # Thin controllers
│ ├─ Middleware/ # Mode/ability enforcement
│ ├─ Requests/ # Validation only
│ └─ Resources/ # API resource transformers
├─ Actions/ # Core use-cases (LoginUser, IssueToken, etc.)
├─ Domain/
│ ├─ Models/ Policies/ Events/ Listeners/ Notifications/
├─ TwoFactor/
│ ├─ Contracts/ Drivers/ # 2FA providers (EmailOtp, SmsOtp, Totp)
├─ Token/
│ ├─ Contracts/ Sanctum/ # Token logic & integration
├─ Session/
│ └─ Csrf/ # SPA session helpers
├─ Support/
│ ├─ DTOs/ Enums/ Helpers/
├─ Exceptions/
├─ Console/
├─ Database/
└─ OpenApi/
tests/

yaml
Copy code

---

## ⚙️ Installation

```bash
composer require rainwaves/lara-auth-suite
php artisan vendor:publish --provider="Rainwaves\\LaraAuthSuite\\LaraAuthSuiteServiceProvider" --tag=authx-config
This publishes the config/authx.php file.

🧪 Local Development Setup
Clone the repo

bash
Copy code
git clone https://github.com/rainwaves/lara-auth-suite.git
cd lara-auth-suite
Install dependencies

bash
Copy code
composer install
Run tests

bash
Copy code
php vendor/bin/pest
(Optional) Create a playground Laravel app to test

bash
Copy code
composer create-project laravel/laravel authsuite-playground
Then in your app’s composer.json:

json
Copy code
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

bash
Copy code
composer update rainwaves/lara-auth-suite
php artisan vendor:publish --provider="Rainwaves\\LaraAuthSuite\\LaraAuthSuiteServiceProvider" --tag=authx-config
php artisan serve
Test endpoint:

http://localhost:8000/auth/ping

🧩 Requirements
PHP 8.2 or higher

Laravel 10.x or 11.x

MySQL / PostgreSQL / SQLite

Laravel Sanctum

Optional integrations:

Twilio / Vonage SDKs (for SMS 2FA)

spomky-labs/otphp (for TOTP)

bacon/bacon-qr-code (for QR provisioning)

🗺️ Roadmap
Phase	Milestone	Description
0	Bootstrap	Service provider, config, routes, tests ✅
1	Token Auth (Sanctum)	Login, logout, me
2	Session Mode	CSRF helper for SPAs
3	Password Reset	Request + reset flows
4	2FA Email OTP	Basic 2FA verification
5	2FA TOTP	Authenticator app support
6	2FA SMS	Twilio / Vonage driver
7	Token & Device Mgmt	Revoke/list/remember devices
8	Docs & Swagger	OpenAPI annotations
9	Harden & Release	CI, coverage, v1.0.0

🧰 Development Stack
Framework: Laravel

Testing: Pest + Orchestra Testbench

Auth: Laravel Sanctum

Docs: Swagger (zircote/swagger-php)

CI: GitHub Actions

🤝 Contributing
Fork the repo

Create your feature branch

bash
Copy code
git checkout -b feat/awesome
Commit your changes

bash
Copy code
git commit -m 'feat: add something awesome'
Push your branch

bash
Copy code
git push origin feat/awesome
Open a pull request 🚀

🛡️ Security
If you discover a security vulnerability, please email security@rainwaves.dev.
Do not open a public GitHub issue.

📄 License
This package is open-source software licensed under the MIT license.

❤️ Credits
Created with pride by Rainwaves

Building secure, scalable Laravel foundations for modern SaaS platforms.

yaml
Copy code

---

Would you like me to add **Swagger integration docs** (section showing how devs can enable `/docs` route and auto-generate OpenAPI YAML from annotations)?  
That would make this README production-ready for Packagist and GitHub.