<?php

namespace LaravelApp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelApp\Models\User;

class AuthTypeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $authType = env('AUTH', 'none');
        
        // Store auth type in config for easy access
        config(['auth.type' => $authType]);
        
        switch ($authType) {
            case 'none':
                // No authentication required
                break;
                
            case 'basic':
                // Apply basic authentication for all routes
                $basicAuth = new \LaravelApp\Http\Middleware\BasicAuthMiddleware();
                return $basicAuth->handle($request, $next);
                
            case 'internal':
                // Laravel auth - check if route requires auth
                if ($request->is('admin/*') && !Auth::check()) {
                    return redirect('/login');
                }
                break;
                
            case 'saml':
                // SAML auth - check if user is authenticated
                if ($request->is('admin/*') && !Auth::check()) {
                    return redirect('/saml/login');
                }
                break;
                
            case 'oauth':
                // OAuth auth - check if user is authenticated
                if ($request->is('admin/*') && !Auth::check()) {
                    return redirect('/oauth/redirect');
                }
                break;
        }
        
        return $next($request);
    }
}
