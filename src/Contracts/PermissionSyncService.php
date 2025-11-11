<?php

namespace Rainwaves\LaraAuthSuite\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface PermissionSyncService
{
    /** Idempotently assign roles/permissions if integration is enabled. */
    public function sync(Authenticatable $user, array $roles = [], array $permissions = []): void;
}
