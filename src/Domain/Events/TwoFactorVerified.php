<?php
namespace Rainwaves\LaraAuthSuite\Domain\Events;
use Illuminate\Contracts\Auth\Authenticatable;
class TwoFactorVerified { public function __construct(public Authenticatable $user, public string $channel) {} }
