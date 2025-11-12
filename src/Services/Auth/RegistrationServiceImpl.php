<?php

namespace Rainwaves\LaraAuthSuite\Services\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Rainwaves\LaraAuthSuite\Contracts\PermissionSyncService;
use Rainwaves\LaraAuthSuite\Contracts\RegistrationService;

readonly class RegistrationServiceImpl implements RegistrationService
{
    public function __construct(
        private PermissionSyncService $permissionSync,
        private string $userModel // from config('authx.user_model')
    ) {}

    public function register(array $data): Authenticatable
    {
        /** @var Model&Authenticatable $user */
        $user = ($this->userModel)::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $roles = $data['roles'] ?? config('authx.registration.default_roles', []);
        $perms = $data['permissions'] ?? config('authx.registration.default_permissions', []);
        $this->permissionSync->sync($user, $roles, $perms);

        return $user;
    }
}
