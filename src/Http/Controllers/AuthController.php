<?php

namespace Rainwaves\LaraAuthSuite\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User; // playground app provides this
use Rainwaves\LaraAuthSuite\Http\Resources\UserResource;

class AuthController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        /** @var \App\Models\User $user */
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke old tokens if needed (optional)
        $user->tokens()->delete();

        // Create token with configured abilities
        $token = $user->createToken(
            'api-token',
            config('authx.tokens.default_abilities', ['*'])
        )->plainTextToken;

        return response()->json([
            'status' => 'ok',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => 'ok', 'message' => 'Logged out']);
    }
}
