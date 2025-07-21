<?php

namespace LaravelApp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelApp\Models\ApplicationToken;
use LaravelApp\Models\User;
use Illuminate\Support\Facades\Auth;

class TokenAuthMiddleware
{
    public function handle(Request $request, Closure $next, string $requiredScope = null)
    {
        $token = $this->extractToken($request);
        
        if (!$token) {
            return response()->json(['error' => 'Token required'], 401);
        }

        $applicationToken = ApplicationToken::findToken($token);
        
        if (!$applicationToken) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        if ($applicationToken->isExpired()) {
            return response()->json(['error' => 'Token expired'], 401);
        }

        // Check required scope
        if ($requiredScope && !$applicationToken->hasScope($requiredScope)) {
            return response()->json(['error' => 'Insufficient permissions'], 403);
        }

        // Add token to request for access in controllers
        $request->attributes->add(['application_token' => $applicationToken]);

        // If this is a user token, authenticate the user
        if ($applicationToken->type === 'user' && $applicationToken->user_id) {
            Auth::loginUsingId($applicationToken->user_id);
        }

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization');
        
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        return substr($header, 7);
    }
}
