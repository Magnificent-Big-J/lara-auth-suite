<?php

namespace Rainwaves\LaraAuthSuite\Services\Auth\PermissionSync;

use Illuminate\Contracts\Auth\Authenticatable;
use Rainwaves\LaraAuthSuite\Contracts\PermissionSyncService;

readonly class SpatiePermissionSyncService implements PermissionSyncService
{
    public function __construct(private bool $enabled) {}

    public function sync(Authenticatable $user, array $roles = [], array $permissions = []): void
    {
        if (! $this->enabled) {
            return;
        }

        // only run if package exists and model has HasRoles trait
        if (! interface_exists(\Spatie\Permission\Contracts\Role::class)) {
            return;
        }
        if (! method_exists($user, 'syncRoles')) {
            return;
        }

        if (! empty($roles)) {
            $user->syncRoles($roles);
        }

        if (method_exists($user, 'syncPermissions') && ! empty($permissions)) {
            $user->syncPermissions($permissions);
        }
    }
}
