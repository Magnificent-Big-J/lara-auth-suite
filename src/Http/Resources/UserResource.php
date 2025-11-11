<?php

namespace Rainwaves\LaraAuthSuite\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (config('authx.permissions.enabled', true)) {
            if (method_exists($this->resource, 'getRoleNames')) {
                $data['roles'] = $this->getRoleNames()->values()->all();
            }
            if (method_exists($this->resource, 'getAllPermissions')) {
                $data['permissions'] = $this->getAllPermissions()->pluck('name')->values()->all();
            }
        }

        return $data;
    }

}
