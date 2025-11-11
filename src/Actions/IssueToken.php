<?php
namespace Rainwaves\LaraAuthSuite\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Rainwaves\LaraAuthSuite\Token\Contracts\TokenManager;

readonly class IssueToken
{
    public function __construct(private TokenManager $tokens) {}
    public function __invoke(Authenticatable $user, array $abilities = ['*']): string { return $this->tokens->issue($user, $abilities); }
}
