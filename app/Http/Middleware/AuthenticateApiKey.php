<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    /**
     * Authenticate user via API Key sent in X-API-Key header.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'API Key diperlukan. Kirim via header X-API-Key.',
            ], 401);
        }

        $user = User::where('api_key', $apiKey)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'API Key tidak valid.',
            ], 401);
        }

        Auth::setUser($user);

        return $next($request);
    }
}
