<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateBasicApi
{
    /**
     * Authenticate user via HTTP Basic Auth (email:password).
     * Returns JSON 401 instead of browser login dialog.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->getUser();
        $password = $request->getPassword();

        if (!$email || !$password) {
            return response()->json([
                'status' => 'error',
                'message' => 'Basic Auth diperlukan (email:password).',
            ], 401, ['WWW-Authenticate' => 'Basic realm="NusaAlert API"']);
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kredensial Basic Auth tidak valid.',
            ], 401);
        }

        Auth::setUser($user);

        return $next($request);
    }
}
