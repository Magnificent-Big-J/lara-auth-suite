<?php
namespace Rainwaves\LaraAuthSuite\Support\Enums;

enum TwoFactorChannel: string { case Email = 'email'; case Sms = 'sms'; case Totp = 'totp'; }
