<?php

namespace LaravelApp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DisableAuthRelatedRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If auth is disabled, block access to auth-related routes
        if (config('auth.type') === 'none') {
            $route = $request->route();
            $routeName = $route ? $route->getName() : '';
            $routeUri = $request->getRequestUri();
            
            // Log for debugging
            Log::info('DisableAuthRelatedRoutes middleware called', [
                'route_name' => $routeName,
                'route_uri' => $routeUri,
                'is_auth_related' => $this->isAuthRelatedRoute($routeName, $routeUri)
            ]);
            
            // Check if this is an auth-related route
            if ($this->isAuthRelatedRoute($routeName, $routeUri)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'This functionality is disabled when authentication is turned off.',
                        'error' => 'AUTH_DISABLED'
                    ], 403);
                }
                
                return redirect()->route('dashboard')->with('error', 
                    'User management and access control features are disabled when authentication is turned off.'
                );
            }
        }
        
        return $next($request);
    }
    
    /**
     * Check if the route is auth-related and should be blocked
     */
    private function isAuthRelatedRoute(string $routeName, string $routeUri): bool
    {
        // Routes that should be blocked when auth=none
        $blockedRouteNames = [
            'users.index',
            'users.create', 
            'users.store',
            'users.show',
            'users.edit',
            'users.update',
            'users.destroy',
            'users.password',
            'users.reset-password',
            'admin.users',
            'access-control.index',
            'access-control.users',
            'access-control.roles',
            'access-control.permissions',
            'access-control.users.role',
        ];
        
        // Check route names
        if (in_array($routeName, $blockedRouteNames)) {
            return true;
        }
        
        // Check URI patterns
        $blockedUriPatterns = [
            '/settings/users',
            '/settings/access-control',
            '/admin/users',
        ];
        
        foreach ($blockedUriPatterns as $pattern) {
            if (str_starts_with($routeUri, $pattern)) {
                return true;
            }
        }
        
        return false;
    }
}
