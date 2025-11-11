<?php
namespace Rainwaves\LaraAuthSuite\Domain\Events;
use Illuminate\Contracts\Auth\Authenticatable;
class TwoFactorChallenged { public function __construct(public Authenticatable $user, public string $channel) {} }
