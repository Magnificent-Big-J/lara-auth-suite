<?php

namespace Rainwaves\LaraAuthSuite\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Rainwaves\LaraAuthSuite\Contracts\AuthService;
use Rainwaves\LaraAuthSuite\Contracts\ITokenAuthService;
use Rainwaves\LaraAuthSuite\Http\Requests\LoginRequest;
use Rainwaves\LaraAuthSuite\Http\Resources\UserResource;
use Rainwaves\LaraAuthSuite\Token\Contracts\TokenManager;

readonly class AuthController
{
    public function __construct(private ITokenAuthService $service) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->service->login(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
        );

        if ($result->requiresTwoFactor) {
            return response()->json([
                'status' => '2fa_required',
                'token' => $result->token,
                'token_type' => $result->tokenType,
                'two_factor' => [
                    'enabled' => true,
                    'verified' => false,
                    'channel' => $result->channel,
                ],
                'user' => new UserResource($result->user),
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'token' => $result->token,
            'token_type' => $result->tokenType,
            'user' => new UserResource($result->user),
        ]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request->user());

        return response()->json([
            'status' => 'ok',
            'message' => 'Logged out',
        ]);
    }
}
