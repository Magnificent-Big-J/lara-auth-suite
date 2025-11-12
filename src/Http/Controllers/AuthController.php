<?php

namespace Rainwaves\LaraAuthSuite\Http\Controllers;

namespace Rainwaves\LaraAuthSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Rainwaves\LaraAuthSuite\Contracts\AuthService;
use Rainwaves\LaraAuthSuite\Http\Requests\LoginRequest;
use Rainwaves\LaraAuthSuite\Http\Resources\UserResource;
use Rainwaves\LaraAuthSuite\Token\Contracts\TokenManager;

readonly class AuthController
{
    public function __construct(
        private AuthService $auth,
        private TokenManager $tokens
    ) {}

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->auth->attemptLogin(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
        );

        $token = $this->tokens->issue(
            $user,
            config('authx.tokens.default_abilities', ['*']),
            config('authx.tokens.expiry_minutes')
        );

        return response()->json([
            'status' => 'ok',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $this->tokens->revokeCurrent($request->user());

        return response()->json(['status' => 'ok', 'message' => 'Logged out']);
    }
}
