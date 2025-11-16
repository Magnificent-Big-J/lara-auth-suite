# 🌊 Rainwaves Lara Auth Suite

Modern, flexible authentication for Laravel APIs & SPAs.

Plug-and-play authentication for Laravel 10/11, supporting both API token auth (Sanctum) and session-based auth for SPAs — with password resets, optional 2FA, and full role/permission support.

---

## 🚀 Overview

Rainwaves/Lara Auth Suite gives you full authentication without writing boilerplate:

- Token authentication for mobile apps or external APIs
- Session authentication for SPAs (Vue / React / Inertia / Livewire)
- Unified password reset flow
- Optional Two-Factor Authentication (email/SMS/TOTP)
- Automatic role & permission assignment (Spatie Permissions)

### Ideal for:

- SaaS platforms
- Admin dashboards
- Multi-tenant SPAs
- Hybrid apps needing both tokens + sessions

---

## ✨ Features

| Feature | Status | Description |
|--------|--------|-------------|
| Sanctum PAT login | ✅ Done | Token-based API authentication |
| Session authentication | ✅ Done | Laravel guard + CSRF protection |
| Password reset (email) | ✅ Done | Full reset flow with throttle |
| 2FA: Email OTP | 🔄 Partial | Enabled if configured |
| 2FA: TOTP | 🔜 Planned | Google Authenticator (QR + verification) |
| 2FA: SMS | 🔜 Planned | Twilio / Vonage driver |
| Trusted devices | 🔜 Planned | Device remembering |
| Token/session/device mgmt | 🔜 Planned | Revoke, audit |

---

## ⚙️ Installation

Install via Composer:

```bash
composer require rainwaves/lara-auth-suite
```

Publish configuration:

```bash
php artisan vendor:publish --provider="Rainwaves\\LaraAuthSuite\\LaraAuthSuiteServiceProvider" --tag=authx-config
```

This publishes:

```
config/authx.php
```

---

## 📦 Usage

Below are the built-in authentication endpoints.

---

### 1. Login (Session Mode)

**POST /auth/login**

Payload:

```json
{
  "email": "admin@example.com",
  "password": "secret",
  "remember": true
}
```

Response:

```json
{
  "status": "ok",
  "user": {}
}
```

---

### 2. Login (Token Mode / API Clients)

**POST /auth/token/login**

Response:

```json
{
  "token": "plain-text-token",
  "abilities": ["*"]
}
```

---

### 3. Get Current User

Requires either:

- Session cookie
- OR Bearer token

**GET /auth/me**

---

### 4. Logout

Session:

```
POST /auth/logout
```

Token:

```
POST /auth/token/logout
```

---

### 5. Forgot Password

**POST /auth/password/forgot**

```json
{ "email": "admin@example.com" }
```

---

### 6. Reset Password

**POST /auth/password/reset**

```json
{
  "email": "admin@example.com",
  "token": "reset-token",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

---

### 7. Two-Factor Authentication (Optional)

Email OTP:

```
POST /auth/session/2fa/email
POST /auth/session/2fa/verify-otp
POST /auth/session/2fa/disable
```

TOTP (future):

```
POST /auth/session/2fa/totp/enable
POST /auth/session/2fa/totp/verify
```

---

## 🔧 Config Example (`config/authx.php`)

```php
return [
    'route_prefix' => 'auth',
    'mode' => 'both',

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
```

---

## 🧩 Frontend Integration (SPA)

A dedicated frontend guide (Vue / Nuxt / React) will cover:

- Login form
- Pinia/Zustand auth store
- Forgot/reset password
- Session cookies
- CSRF handling
- Auto-refresh bootstrap
- 2FA screens
- Role & permission-based UI

---

## 🛣 Roadmap

| Phase | Feature |
|-------|---------|
| 1 | Token auth (done) |
| 2 | Session auth (done) |
| 3 | Password reset (done) |
| 4 | 2FA Email (done)|
| 5 | 2FA TOTP |
| 6 | 2FA SMS |
| 7 | Trusted devices |
| 8 | Token/session management |
| 9 | Frontend documentation |
| 10 | v1.0 stable release |

---

## 🛡 Security

Report vulnerabilities to:

📧 **security@rainwaves.dev**

---

## 📄 License

MIT © Rainwaves

---

## ❤️ Credits

Created with love by **Rainwaves**  
Building secure, modern SaaS authentication for Laravel.

---

## ✅ Ready for the Frontend Guide?

Say:

**"Start the frontend-only chat"**
