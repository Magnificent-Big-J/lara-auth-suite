<?php

namespace Rainwaves\LaraAuthSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Rainwaves\LaraAuthSuite\Contracts\PasswordResetService;
use Rainwaves\LaraAuthSuite\Http\Requests\PerformPasswordResetRequest;
use Rainwaves\LaraAuthSuite\Http\Requests\RequestPasswordResetRequest;

readonly class PasswordResetController
{
    public function __construct(private PasswordResetService $service) {}

    public function request(RequestPasswordResetRequest $request): JsonResponse
    {
        $this->service->requestReset($request->email);

        return response()->json(['status' => 'ok', 'message' => 'Password reset link sent']);
    }

    public function reset(PerformPasswordResetRequest $request): JsonResponse
    {
        $ok = $this->service->resetPassword($request->email, $request->token, $request->password);

        return $ok
            ? response()->json(['status' => 'ok', 'message' => 'Password has been reset'])
            : response()->json(['status' => 'error', 'message' => 'Invalid or expired token'], 400);
    }
}
