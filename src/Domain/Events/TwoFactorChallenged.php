<?php
namespace Rainwaves\LaraAuthSuite\Domain\Events;
use Illuminate\Contracts\Auth\Authenticatable;
class TwoFactorChallenged { public function __construct(public Authenticicatable $user, public string $channel) {} }
