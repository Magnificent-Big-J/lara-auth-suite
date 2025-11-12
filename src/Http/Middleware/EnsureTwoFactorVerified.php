<?php

namespace Rainwaves\LaraAuthSuite\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rainwaves\LaraAuthSuite\TwoFactor\Contracts\ITwoFactorAuth;
use Symfony\Component\HttpFoundation\Response;

readonly class EnsureTwoFactorVerified
{
    public function __construct(private ITwoFactorAuth $twofa) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $this->twofa->isVerified($user)) {
            return response()->json([
                'status' => 'error',
                'code' => '2fa_required',
                'message' => 'Two-factor verification required.',
            ], 403);
        }

        return $next($request);
    }
}
