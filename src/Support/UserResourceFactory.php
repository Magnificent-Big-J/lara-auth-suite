<?php

namespace Rainwaves\LaraAuthSuite\Support;

use Illuminate\Http\Resources\Json\JsonResource;
use Rainwaves\LaraAuthSuite\Http\Resources\UserResource as PackageUserResource;

class UserResourceFactory
{
    public function make(mixed $user): JsonResource
    {
        /** @var class-string<JsonResource>|null $resourceClass */
        $resourceClass = config('authx.user_resource');

        $resourceClass = $resourceClass ?: PackageUserResource::class;

        // If host supplies something invalid, fallback safely
        if (! is_string($resourceClass) || ! is_subclass_of($resourceClass, JsonResource::class)) {
            $resourceClass = PackageUserResource::class;
        }

        return $resourceClass::make($user);
    }
}
