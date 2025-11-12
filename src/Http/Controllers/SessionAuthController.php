<?php

namespace Rainwaves\LaraAuthSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Rainwaves\LaraAuthSuite\Contracts\SessionAuthService;
use Rainwaves\LaraAuthSuite\Http\Requests\LoginRequest;
use Rainwaves\LaraAuthSuite\Http\Resources\UserResource;

readonly class SessionAuthController
{
    public function __construct(private SessionAuthService $service) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->service->login(
            $request->string('email')->toString(),
            $request->string('password')->toString()
        );

        // Important: rotate session ID after login
        $request->session()->regenerate();

        return response()->json([
            'status' => 'ok',
            'user' => new UserResource($user),
        ]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($this->service->current());
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout();

        return response()->json(['status' => 'ok', 'message' => 'Logged out']);
    }
}
