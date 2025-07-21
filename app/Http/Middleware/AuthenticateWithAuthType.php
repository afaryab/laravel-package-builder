<?php

namespace LaravelApp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithAuthType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is already authenticated, continue
        if (Auth::check()) {
            return $next($request);
        }

        // User is not authenticated, redirect based on AUTH type
        $authType = env('AUTH', 'none');
        
        switch ($authType) {
            case 'internal':
                // Redirect to internal login page
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Unauthenticated'], 401);
                }
                return redirect()->route('login');
                
            case 'oauth':
                // Redirect to OAuth provider
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Unauthenticated', 'auth_url' => route('oauth.redirect')], 401);
                }
                return redirect()->route('oauth.redirect');
                
            case 'saml':
                // Redirect to SAML login
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Unauthenticated', 'auth_url' => route('saml.login')], 401);
                }
                return redirect()->route('saml.login');
                
            case 'none':
            default:
                // No authentication configured or required
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Authentication not configured'], 401);
                }
                return redirect('/')->with('error', 'Authentication is required but not configured.');
        }
    }
}
