<?php

namespace Rainwaves\LaraAuthSuite\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\ITwoFactorAuth;

readonly class TwoFactorManageController
{
    public function __construct(private ITwoFactorAuth $twofa) {}

    public function status(Request $request): JsonResponse
    {
        return response()->json(
            $this->twofa->getStatus($request->user())
        );
    }

    public function emailChallenge(Request $request): JsonResponse
    {
        $this->twofa->enableEmailOtp($request->user());
        $this->twofa->sendEmailOtp($request->user());

        return response()->json(['status' => 'ok', 'message' => 'Email code sent']);
    }

    public function smsChallenge(Request $request): JsonResponse
    {
        $data = $request->validate(['phone' => ['required', 'string']]);
        $this->twofa->sendSmsOtp($request->user(), $data['phone']);

        return response()->json(['status' => 'ok', 'message' => 'SMS code sent']);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => ['required', 'string']]);
        $ok   = $this->twofa->verifyOtp($request->user(), $data['code']);

        return $ok
            ? response()->json(['status' => 'ok', 'message' => '2FA verified'])
            : response()->json(['status' => 'error', 'message' => 'Invalid or expired code'], 422);
    }

    public function enableTotp(Request $request): JsonResponse
    {
        $secret = $this->twofa->enableAuthenticatorApp($request->user());

        return response()->json(['status' => 'ok', 'secret' => $secret]);
    }

    public function verifyTotp(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => ['required', 'string']]);
        $ok   = $this->twofa->verifyAuthenticatorApp($request->user(), $data['code']);

        return $ok
            ? response()->json(['status' => 'ok', 'message' => 'Authenticator enabled'])
            : response()->json(['status' => 'error', 'message' => 'Invalid code'], 422);
    }

    public function disable(Request $request): JsonResponse
    {
        $this->twofa->disableTwoFactor($request->user());

        return response()->json(['status' => 'ok', 'message' => '2FA disabled']);
    }
}
