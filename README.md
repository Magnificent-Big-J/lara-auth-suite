# 🌊 Rainwaves Lara Auth Suite

Modern, flexible authentication for Laravel APIs & SPAs.

Plug-and-play authentication for Laravel 10/11, supporting both API token auth (Sanctum) and session-based auth for SPAs — with password resets, backend‑enforced Two‑Factor Authentication, and full role/permission support.

---

## 🚀 Overview

Rainwaves/Lara Auth Suite gives you full authentication without writing boilerplate:

- Token authentication for mobile apps or external APIs
- Session authentication for SPAs (Vue / React / Inertia / Livewire)
- Unified password reset flow
- Two‑Factor Authentication (Email OTP, Authenticator App)
- Automatic role & permission assignment (Spatie Permissions)

### Ideal for:

- SaaS platforms
- Admin dashboards
- Multi‑tenant SPAs
- Hybrid apps needing both tokens + sessions

---

## 🧪 Demo Applications

### Backend (Laravel)

Reference backend implementation using the package:

https://github.com/Magnificent-Big-J/lara-auth-suite-demo

### Frontend (Nuxt SPA)

Full SPA login + 2FA flow:

https://github.com/Magnificent-Big-J/lara-auth-suite-nuxt-demo

---

## ✨ Features

| Feature | Status | Description |
|--------|--------|-------------|
| Sanctum PAT login | ✅ Done | Token‑based API authentication |
| Session authentication | ✅ Done | Laravel guard + CSRF protection |
| Password reset (email) | ✅ Done | Full reset flow with throttle |
| 2FA: Email OTP | ✅ Done | Secure email verification codes |
| 2FA: TOTP (Authenticator App) | ✅ Done | Google Authenticator / Authy / 1Password |
| 2FA enforcement | ✅ Done | Enforced during login (backend‑driven) |
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
php artisan vendor:publish \
  --provider="Rainwaves\\LaraAuthSuite\\LaraAuthSuiteServiceProvider" \
  --tag=authx-config
```

This publishes:

```text
config/authx.php
```

---

## 🔐 Authentication Flow (Important)

Authentication decisions are enforced **on the backend**.
Frontend clients **do not decide** authentication state.

- Credentials are validated
- Session or token is issued
- Two‑Factor policy is evaluated immediately
- User is **not fully authenticated** until 2FA is verified (if required)

This prevents:

- Logged‑in‑but‑unverified states
- Session persistence before verification
- Frontend‑controlled security decisions

---

## 📦 Usage

Below are the built‑in authentication endpoints.

---

### 1. Login (Session Mode – SPA)

**POST /auth/session/login**

Payload:

```json
{
  "email": "admin@example.com",
  "password": "secret",
  "remember": true
}
```

Response (2FA required):

```json
{
  "user": {},
  "requires_two_factor": true,
  "channel": "email"
}
```

Response (2FA not required):

```json
{
  "user": {},
  "requires_two_factor": false
}
```

---

### 2. Login (Token Mode / API Clients)

**POST /auth/login**

Payload:

```json
{
  "email": "admin@example.com",
  "password": "secret"
}
```

Response:

```json
{
  "token": "plain-text-token",
  "token_type": "Bearer",
  "abilities": ["*"]
}
```

> Token‑based 2FA enforcement is supported via middleware.

---

### 3. Get Current User

Requires either:

- Session cookie
- OR Bearer token

**GET /auth/me**

---

### 4. Logout

Session:

```text
POST /auth/session/logout
```

Token:

```text
POST /auth/logout
```

---

### 5. Forgot Password

**POST /auth/password/forgot**

```json
{
  "email": "admin@example.com"
}
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

## 🔐 Two‑Factor Authentication

Two‑Factor Authentication is:

- Evaluated during login
- Enforced by backend services
- Independent of frontend auth state

### Email OTP

```text
POST /auth/session/2fa/email
POST /auth/session/2fa/verify-otp
POST /auth/session/2fa/disable
```

### Authenticator App (TOTP)

```text
POST /auth/session/2fa/totp/enable
POST /auth/session/2fa/totp/verify
```

> SMS‑based OTP is intentionally excluded due to SIM‑swap risk.

---

## 🔧 Configuration (`config/authx.php`)

```php
return [
    'route_prefix' => 'auth',
    'mode' => 'both',

    'features' => [
        'password_reset',
        'two_factor',
        'tokens',
    ],

    '2fa' => [
        'channels' => ['email', 'totp'],
        'enforcement' => 'optional', // off | optional | required
    ],
];
```

---

## 🧩 Frontend Integration (SPA)

Frontend clients consume backend decisions.
They do **not** determine authentication state.

The backend returns:

- Whether 2FA is required
- Which channel must be used
- Whether the session/token is verified

Reference implementation:

https://github.com/Magnificent-Big-J/lara-auth-suite-nuxt-demo

---

## 🛣 Roadmap

| Phase | Feature                |
|-------|------------------------|
| 1 | Token authentication   |
| 2 | Session authentication |
| 3 | Password reset         |
| 4 | Email OTP              |
| 5 | TOTP                   |
| 6 | Trusted devices        |
| 7 | Session/token audit    |
| 8 | Frontend documentation |
| 9 | v1.0.2 stable release  |

---

## 🛡 Security

Report security issues to:

📧 security@rainwaves.dev

---

## 📄 License

MIT © Rainwaves

---

## ❤️ Credits

Built by **Rainwaves**  
Security‑first authentication for serious Laravel applications.
