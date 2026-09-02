<?php

namespace App\Http\Middleware;

use App\Support\JwtToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnsureJwtTokenIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $header = (string) $request->header('Authorization', '');

        if (! str_starts_with($header, 'Bearer ')) {
            return $this->unauthorized();
        }

        $token = trim(substr($header, 7));

        if ($token === '' || ! JwtToken::validate($token)) {
            return $this->unauthorized();
        }

        return $next($request);
    }

    private function unauthorized(): JsonResponse
    {
        return response()->json([
            'ok' => false,
            'message' => 'Unauthorized.',
        ], 401);
    }
}
