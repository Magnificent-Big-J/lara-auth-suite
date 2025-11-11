<?php

namespace Rainwaves\LaraAuthSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Rainwaves\LaraAuthSuite\Contracts\RegistrationService;
use Rainwaves\LaraAuthSuite\Http\Requests\RegisterRequest;
use Rainwaves\LaraAuthSuite\Http\Resources\UserResource;
use Rainwaves\LaraAuthSuite\Token\Contracts\TokenManager;
use Rainwaves\LaraAuthSuite\Exceptions\RegistrationDisabled;
use Rainwaves\LaraAuthSuite\Exceptions\ValidationFailed;

readonly class RegistrationController
{
    public function __construct(
        private RegistrationService $registrations,
        private TokenManager        $tokens
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        if (! config('authx.registration.enabled', true)) {
            throw new RegistrationDisabled('Registration is disabled');
        }

        try {
            $user = $this->registrations->register($request->validated());
        } catch (ValidationException $e) {
            throw ValidationFailed::from($e);
        }

        $response = ['status' => 'ok', 'user' => new UserResource($user)];

        if (config('authx.registration.issue_token_on_register', true)) {
            $token = $this->tokens->issue($user, config('authx.tokens.default_abilities', ['*']));
            $response['token'] = $token;
            $response['token_type'] = 'Bearer';
        }

        return response()->json($response, 201);
    }
}
